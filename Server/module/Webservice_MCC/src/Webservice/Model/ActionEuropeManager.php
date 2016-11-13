<?php

namespace Webservice\Model;

use Application\MccSimpleLogger;
use Application\MccSystem;
use Application\MccXmlWrapper;
use Doctrine\ORM\EntityManager;
use Order\Entity\ExternalOrderNumber;
use Order\Entity\Order;
use Order\Entity\Waybill;
use Warehouse\Repository\CacheRepository;
use Webservice\Entity\ActionEuropeFeed;
use Webservice\Enums\ActionEuropeFeedStatusType;
use Webservice\Enums\ActionEuropeFeedType;
use Webservice\Enums\ActionEuropeNotificationEventType;
use Webservice\Enums\ActionEuropeOrderStatusType;
use Webservice\Enums\ConfigurationOrderStatusName;
use ZipArchive;

/**
 * Class for managing all operations associated with ActionEurope
 * @package Webservice\Model
 */
class ActionEuropeManager
{

	/**
	 * @var EntityManager
	 */
	protected $entityManager;
	/**
	 * @var ActionEuropeClient
	 */
	protected $actionEuropeClient;
	/**
	 * @var array
	 */
	protected $configuration;

	/**
	 * @var \Document\Repository\OracleMethodRepository
	 */
	protected $oracleMethodRepository;

	/**
	 * @var \Webservice\Repository\ActionEuropeOrderRepository $actionEuropeOrderRepository
	 */
	private $actionEuropeOrderRepository;

	/**
	 * @var \Webservice\Repository\ActionEuropeFeedRepository $actionEuropeFeedRepository
	 */
	private $actionEuropeFeedRepository;

	private $orderMainStatusRepository;


	/**
	 * ActionEuropeManager constructor.
	 *
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->configuration = MccSystem::getConfig('IntegrationSettings')['ActionEurope'];
	}

	//region Common used repository getters
	/**
	 * @return \Webservice\Repository\ActionEuropeOrderRepository
	 */
	private function getActionEuropeOrderRepository()
	{
		if ($this->actionEuropeOrderRepository === null) {
			$this->actionEuropeOrderRepository = $this->entityManager->getRepository('Webservice\Entity\ActionEuropeOrder');
		}
		return $this->actionEuropeOrderRepository;
	}

	/**
	 * @return \Webservice\Repository\ActionEuropeFeedRepository
	 */
	private function getActionEuropeFeedRepository()
	{
		if ($this->actionEuropeFeedRepository === null) {
			$this->actionEuropeFeedRepository = $this->entityManager->getRepository('Webservice\Entity\ActionEuropeFeed');
		}
		return $this->actionEuropeFeedRepository;
	}

	/**
	 * @return \Order\Repository\OrderMainStatusRepository
	 */
	private function getOrderMainStatusRepository()
	{
		if ($this->orderMainStatusRepository === null) {
			$this->orderMainStatusRepository = $this->entityManager->getRepository('Order\Entity\OrderMainStatus');
		}
		return $this->orderMainStatusRepository;
	}
	//endregion

	/**
	 * processes the stored feed asynchronously
	 * @param int $feedId the id of the earlier stored feed
	 * @return int
	 * @throws \Exception
	 * @throws array
	 * @throws string
	 */
	public function processOrderFeed($feedId)
	{
		$entityManager = $this->entityManager;
		$entitiesToBeSaved = [];
		/** @var \Webservice\Repository\ActionEuropeFeedRepository $feedRepository */
		$feedRepository = $entityManager->getRepository('\Webservice\Entity\ActionEuropeFeed');
		/** @var \Webservice\Entity\ActionEuropeFeed $feed */
		$feed = $feedRepository->find($feedId);
		$xmlData = str_replace('bmecat:', '', $feed->getFeedData());

		$xmlElement = new MccXmlWrapper($xmlData, ['x' => 'http://www.opentrans.org/XMLSchema/2.1']);
		$xmlOrderId = $xmlElement->xpathSingle('//x:ORDER_ID');
		if ($xmlOrderId === null) {
			MccSimpleLogger::log('ActionEurope\orderProcess', ['feedId' => $feedId, 'message' => 'Invalid XML file']);
			throw new \Exception("No order id in this feed");
		}
		$orderId = intval(current(explode('-', $xmlOrderId)));
		/** @var \Order\Repository\OrderRepository $orderRepository */
		$orderRepository = $entityManager->getRepository('Order\Entity\Order');
		$this->getOrderMainStatusRepository();


		/** @var Order $order */
		$order = $orderRepository->find($orderId);
		if (null === $order) {
			MccSimpleLogger::log('ActionEurope\orderProcess', ['feedId' => $feedId, 'message' => 'No matching order found'], null, true);
		}
		$actionEuropeOrderStatus = $order->actionEuropeStatus;
		if ($actionEuropeOrderStatus == null) {
			MccSimpleLogger::log('ActionEurope\orderProcess', ['feedId' => $feedId, 'message' => 'No matching order status found'], null, true);
		}
		$supplierOrderId = $xmlElement->xpathSingle('//x:SUPPLIER_ORDER_ID');
		$orderStatuses = $this->configuration['orderStatuses'];
		$feed->orderId = $orderId;
		$externalOrderNumberType = $this->configuration['externalOrderNumberType'];
		$externalOrderNumbers = $order->externalOrderNumber->filter(function (\Order\Entity\ExternalOrderNumber $number) use ($externalOrderNumberType) {
			return $number->type == $externalOrderNumberType;
		});
		$hasExternalNumber = ($externalOrderNumbers->count() > 0);
		if ($hasExternalNumber) {
			$externalOrderNumber = $externalOrderNumbers->first();
		}else{
			$externalOrderNumber = new ExternalOrderNumber();
			$externalOrderNumber->idOrder = $order;
			$externalOrderNumber->externalOrder = $supplierOrderId;
			$externalOrderNumber->type = $externalOrderNumberType;
		}

		switch ($feed->feedType) {
			case ActionEuropeFeedType::AE_ORDER_RESPONSE:
				if (in_array($actionEuropeOrderStatus->status, [
					ActionEuropeOrderStatusType::AE_ORDER_STATUS_PENDING_RESERVATION,
					ActionEuropeOrderStatusType::AE_ORDER_STATUS_RESERVATION_SUCCESSFUL,
					ActionEuropeOrderStatusType::AE_ORDER_STATUS_FULFILLMENT_REQUEST_SENT,
				])) {
					if (!empty($supplierOrderId)) {
						$externalOrderNumber->status = 'Reserved';
						if (!$hasExternalNumber) {
							$order->externalOrderNumber->add($externalOrderNumber);
						}
						$actionEuropeOrderStatus->status = ActionEuropeOrderStatusType::AE_ORDER_STATUS_RESERVATION_CONFIRMED;
						$newOrderStatusName = ConfigurationOrderStatusName::PENDING_FULFILLMENT;
						$feed->feedStatus = ActionEuropeFeedStatusType::AE_SUCCESS;
						$entitiesToBeSaved[] = $externalOrderNumber;
					} else {
						$errorMsg = $xmlElement->xpathSingle('//x:ORDERRESPONSE_INFO/x:REMARKS[@type=\'orderresponse\']');
						if (!empty($errorMsg)) {
							$order->error = "Reservation failed: " . $errorMsg;
							MccSimpleLogger::log('ActionEurope\orderProcess', ['feedId' => $feedId, 'message' => $errorMsg]);
						}
						$actionEuropeOrderStatus->status = ActionEuropeOrderStatusType::AE_ORDER_STATUS_NONE;
						$feed->feedStatus = ActionEuropeFeedStatusType::AE_SUCCESS;
						$newOrderStatusName = ConfigurationOrderStatusName::RESERVATION_FAILED;
					}
				} else {
					$feed->feedStatus = ActionEuropeFeedStatusType::AE_IGNORED;
				}
				break;
			case ActionEuropeFeedType::AE_DELIVERY_NOTE:
				if (in_array($actionEuropeOrderStatus->status, [
					ActionEuropeOrderStatusType::AE_ORDER_STATUS_FULFILLMENT_REQUEST_SENT,
					ActionEuropeOrderStatusType::AE_ORDER_STATUS_RESERVATION_SUCCESSFUL,
					ActionEuropeOrderStatusType::AE_ORDER_STATUS_RESERVATION_CONFIRMED,
				])) {
					$dispatchNotificationId = $xmlElement->xpathSingle('//x:SHIPMENT_ID');
					if (!empty($dispatchNotificationId)) {
						$deliveryNoteNumber = $dispatchNotificationId;
						/** @noinspection PhpUnusedParameterInspection */
						$wayBillExists = $order->deliveryWaybills->exists(function ($i, Waybill $w) use ($deliveryNoteNumber) {
							return $w->number == $deliveryNoteNumber;
						});
						if (!$wayBillExists) {
							$wayBill = new Waybill();
							$wayBill->number = $deliveryNoteNumber;
							$order->addDeliveryWaybills($wayBill);
							$entitiesToBeSaved[] = $wayBill;
						}
						$actionEuropeOrderStatus->status = ActionEuropeOrderStatusType::AE_ORDER_STATUS_FULFILLED;
						$newOrderStatusName = ConfigurationOrderStatusName::PACKED;
						$externalOrderNumber->status = 'Sent';
						$feed->feedStatus = ActionEuropeFeedStatusType::AE_SUCCESS;
						$entitiesToBeSaved[] = $externalOrderNumber;
					} else {
						$newOrderStatusName = ConfigurationOrderStatusName::PACKING_FAILED;
						$feed->feedStatus = ActionEuropeFeedStatusType::AE_SUCCESS;
					}
				} else {
					$feed->feedStatus = ActionEuropeFeedStatusType::AE_IGNORED;
				}
				break;
			case ActionEuropeFeedType::AE_INVOICE:
				if ($actionEuropeOrderStatus->status == ActionEuropeOrderStatusType::AE_ORDER_STATUS_FULFILLED) {
					try {
						$this->handleInvoice($order, $xmlData);
						$newOrderStatusName = ConfigurationOrderStatusName::INVOICE_CREATED;
						$actionEuropeOrderStatus->status = ActionEuropeOrderStatusType::AE_ORDER_STATUS_INVOICE_CREATED;
						$externalOrderNumber->status = 'Realized';
						$feed->feedStatus = ActionEuropeFeedStatusType::AE_SUCCESS;
						$entitiesToBeSaved[] = $externalOrderNumber;
					} catch (\Exception $exception) {
						$newOrderStatusName = ConfigurationOrderStatusName::INVOICE_FAILED;
						$feed->feedStatus = ActionEuropeFeedStatusType::AE_ERROR;
						MccSimpleLogger::log('ActionEurope\orderProcess', $exception, null, null);
					}
				} else {
					$feed->feedStatus = ActionEuropeFeedStatusType::AE_IGNORED;
				}
				break;
		}

		$newOrderStatus = (!empty($newOrderStatusName)) ? $this->getOrderMainStatusRepository()->getStatusByNumber($orderStatuses[$newOrderStatusName]) : null;
		if (!empty($newOrderStatus)) {
			$order->idOrderMainStatus = $newOrderStatus;
		}
		$entitiesToBeSaved = array_merge($entitiesToBeSaved, [$order, $actionEuropeOrderStatus, $feed]);
		foreach ($entitiesToBeSaved as $entityToBeSaved) {
			$entityManager->persist($entityToBeSaved);
		}
		$entityManager->flush($entitiesToBeSaved);
		return 0;
	}


	/**
	 * Get webservice client for Action Europe.
	 *
	 * @return ActionEuropeClient
	 */
	protected function getActionEuropeClient()
	{
		if (empty($this->actionEuropeClient)) {
			$this->actionEuropeClient = new ActionEuropeClient($this->entityManager, $this->configuration);
		}
		return $this->actionEuropeClient;
	}


	/**
	 * Update warehouse stocks.
	 *
	 * @param bool|true $verbose
	 */
	public function updateWarehouseStocks($verbose = true)
	{
		/** @var CacheRepository $cacheRepository */
		$cacheRepository = $this->entityManager->getRepository('Product\Entity\MagazineDefinition');
		$cacheRepository->setVerbose($verbose);
		$warehouses = $cacheRepository->getWarehouses('ActionEurope');
		$systemProducts = $this->entityManager->createQueryBuilder()
			->select('pv.id', 'pc.code')
			->from('Product\Entity\ProductCode', 'pc', 'pc.code')
			->join('pc.productVersion', 'pv')
			->where('pc.type = :hostProductCode')
			->setParameter('hostProductCode', $this->configuration['codeTypeId'])
			->getQuery()->getArrayResult();

		$productIds = [];
		foreach ($systemProducts as $systemProduct) {
			$productIds[$systemProduct['code']][] = $systemProduct['id'];
		}
		$actionEuropeProducts = $this->getActionEuropeClient()->getProducts();

		$logs = [];
		foreach ($actionEuropeProducts as $actionEuropeProduct) {
			if (!empty($productIds[$actionEuropeProduct['ProductId']]) && $actionEuropeProduct['Price'] > 0 && $actionEuropeProduct['Quantity'] > 0) {
				foreach ($productIds[$actionEuropeProduct['ProductId']] as $versionId) {
					$warehouseId = $this->configuration['warehouseId'];
					$warehouses[$warehouseId]['stockDefinitions'][$versionId] = [
						'netStock'      => $actionEuropeProduct['Quantity'],
						'physicalStock' => $actionEuropeProduct['Quantity'],
						'sellableStock' => $actionEuropeProduct['Quantity'],
					];
					$warehouses[$warehouseId]['pricingDefinitions'][$versionId] = round($actionEuropeProduct['Price'], 2);
					if ($this->configuration['enableLogging']) {
						$logs[] = [
							'VersionId'     => $versionId,
							'ProductId'     => $actionEuropeProduct['ProductId'],
							'Quantity'      => $actionEuropeProduct['Quantity'],
							'Price'         => $actionEuropeProduct['Price'],
							'Currency'      => 'EUR',
							'CurrencyPrice' => round($actionEuropeProduct['Price'], 2),
							'Warehouse'     => $this->configuration['warehouseId'],
						];
					}
				}
			}
		}

		if ($this->configuration['enableLogging']) {
			$this->log($logs);
		}

		foreach ($warehouses as $warehouse) {
			if (!isset($warehouse['stockDefinitions'])) {
				$warehouse['stockDefinitions'] = [];
			}
			if (!isset($warehouse['pricingDefinitions'])) {
				$warehouse['pricingDefinitions'] = [];
			}
			$cacheRepository->updateWarehouse($warehouse['warehouseId'], $warehouse['stockDefinitions'], true);
			$cacheRepository->updatePricing($warehouse['pricingId'], $warehouse['pricingDefinitions'], true);
		}

	}

	/**
	 * @return array array with values useful for logging:
	 * valueA:  total orders count
	 * valueB:  successfully reserved orders
	 */
	public function reserveOrders()
	{
		/** @var \Webservice\Repository\ActionEuropeOrderRepository $actionEuropeOrderRepository */
		$actionEuropeOrderRepository = $this->entityManager->getRepository('Webservice\Entity\ActionEuropeOrder');
		$orders = $actionEuropeOrderRepository->findOrdersForReservationSending($this->configuration['warehouseId'], $this->configuration['codeTypeId'], $this->configuration['orderStatuses'][ConfigurationOrderStatusName::PENDING_RESERVATION]);
		$successful = 0;
		$total = count($orders);
		foreach ($orders as $order) {
			if ($this->reserveOrder($order)) {
				$successful++;
			}
		}
		return [
			'total'      => $total,
			'successful' => $successful,
		];

	}

	/**
	 * Send a product reservation to ActionEurope
	 * @param Order $order
	 * @return bool
	 */
	public function reserveOrder(Order $order)
	{
		$entityManager = $this->entityManager;
		/** @var \Order\Repository\OrderMainStatusRepository $orderMainStatusRepository */
		$orderMainStatusRepository = $entityManager->getRepository('Order\Entity\OrderMainStatus');
		/** @var \Webservice\Repository\ActionEuropeOrderRepository $actionEuropeOrderRepository */
		$actionEuropeOrderRepository = $this->getActionEuropeOrderRepository($entityManager);
		$data = $this->generateOrderXml($order);
		$feed = $this->createXmlFeed($data, ActionEuropeFeedType::AE_ORDER_REQUEST);
		$entitiesToFlush = [$feed];
		$client = $this->getActionEuropeClient();
		$feed->orderId = $order->id;

		$actionEuropeOrder = $actionEuropeOrderRepository->findOneByOrderOrCreate($order);
		$result = $client->postOrder($data);
		$orderStatuses = $this->configuration['orderStatuses'];

		if ($result['code'] == 200) {
			$feed->feedStatus = ActionEuropeFeedStatusType::AE_SUCCESS;
			$actionEuropeOrder->status = ActionEuropeOrderStatusType::AE_ORDER_STATUS_PENDING_RESERVATION;
			$newOrderStatus = $orderStatuses[ConfigurationOrderStatusName::AWAITING_CONFIRMATION];
		} else {
			$order->error = "Action Europe order request not successful";
			$feed->feedStatus = ActionEuropeFeedStatusType::AE_ERROR;
			$actionEuropeOrder->status = ActionEuropeOrderStatusType::AE_ORDER_STATUS_NONE;
			$newOrderStatus = $orderStatuses[ConfigurationOrderStatusName::RESERVATION_FAILED];
		}
		$order->idOrderMainStatus = $orderMainStatusRepository->getStatusByNumber($newOrderStatus);

		$entitiesToFlush[] = $order;
		$entitiesToFlush[] = $actionEuropeOrder;
		foreach ($entitiesToFlush as $entityToFlush) {
			$entityManager->persist($entityToFlush);
		}
		$entityManager->flush($entitiesToFlush);
		return $result;
	}


	/**
	 * Log stock changes.
	 *
	 * @param array $logs
	 *
	 * @return bool
	 */
	public function log($logs)
	{
		$logPath = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/data/Csv/ActionEuropeStock/' . date('Y/m/d/');
		if (!file_exists($logPath)) {
			mkdir($logPath, 0777, true);
		}
		$logFilePath = $logPath . date('His');
		if (!is_file($logFilePath)) {
			touch($logFilePath);
		}
			if (!is_writable($logFilePath)) {
			return false;
		}

		$logFile = fopen($logFilePath, 'a');
		$header = true;
		foreach ($logs as $log) {
			if ($header) {
				fputcsv($logFile, array_keys($log));
				$header = false;
			}
			fputcsv($logFile, $log);
		}
		fclose($logFile);

		$zip = new ZipArchive();
		$zipFile = $logFilePath . '.zip';
		if ($zip->open($zipFile, ZipArchive::CREATE) !== TRUE) {
			return false;
		}
		$zip->addFile($logFilePath, basename($logFilePath) . '.csv');
		$zip->close();
		unlink($logFilePath);

		return true;
	}


	/**
	 * Generates XML string for a certain order
	 * @param Order $order
	 * @return string
	 */
	private function generateOrderXml(Order $order)
	{
		$attributesTableTemplate = file_get_contents(__DIR__ . '/../../../view/webservice/xml/order-request.twig');

		/** @var \Webservice\Repository\ActionEuropeFeedRepository $feedRepository */
		$feedRepository = $this->entityManager->getRepository('\Webservice\Entity\ActionEuropeFeed');

		$customOrderId = $feedRepository->getOrderIdWithFeedCount($order->id, false);

		$orderModel = new ActionEuropeOrderModel($order, $this->configuration, $customOrderId);
		$xml = trim(\Template\Model\TemplateManager::getHTML($attributesTableTemplate, [
			'companyId' => $this->configuration['eserviceKdnr'], 'order' => $orderModel]));
		return $xml;
	}

	/**
	 * @param $data
	 * @param int|null $feedType the type of the feed (ex. request or response)
	 * @return bool|ActionEuropeFeed newly created feed or false on fail
	 * @throws \Exception
	 * @throws array
	 * @throws string
	 */
	public function createXmlFeed($data, $feedType = null)
	{
		$xmlElement = new \SimpleXMLElement($data);
		if ($feedType === null) {
			$typeName = $xmlElement->getName();
			$feedType = array_search($typeName, [
				ActionEuropeFeedType::AE_ORDER_RESPONSE => 'ORDERRESPONSE',
				ActionEuropeFeedType::AE_DELIVERY_NOTE  => 'DISPATCHNOTIFICATION',
				ActionEuropeFeedType::AE_INVOICE        => 'INVOICE',
			]);
			if (false === $feedType) {
				MccSimpleLogger::log('ActionEurope\xmlFeeds', ['msg' => 'unknown feed type!', 'rawData' => $data], 'xmlError');
				throw new \Exception("Unknown XML feed type!");
			}
		}
		$entityManager = $this->entityManager;
		$feed = new ActionEuropeFeed();
		$feed->feedDate = new \DateTime();
		$feed->feedType = $feedType;
		$feed->feedStatus = ActionEuropeFeedStatusType::AE_UNPROCESSED;
		$entityManager->persist($feed);
		$entityManager->flush($feed);
		if (false !== $feed->setFeedData($xmlElement->asXML())) {
			return $feed;
		} else {
			MccSimpleLogger::log('ActionEurope\orderResponse', ['rawData' => $data], 'xmlNotSaved');
			return false;
		}
	}

	/**
	 * @param int $notificationType const from ActionEuropeNotificationEventType
	 * @return array
	 * @throws \Exception
	 * @throws array
	 * @throws string
	 */
	public function notifyByMail($notificationType)
	{
		$entityManager = $this->entityManager;
		$actionEuropeOrderRepository = $this->getActionEuropeOrderRepository();
		$actionEuropeFeedRepository = $this->getActionEuropeFeedRepository();


		$orderStatusesConfig = $this->configuration['orderStatuses'];
		if ($notificationType == ActionEuropeNotificationEventType::AE_AWAITING_FULFILLMENT) {
			$orders = $actionEuropeOrderRepository->findOrdersForReservationNotification($orderStatusesConfig[ConfigurationOrderStatusName::RESERVED]);
		} else {
			$orders = $actionEuropeOrderRepository->findOrderForCancellationNotification($orderStatusesConfig[ConfigurationOrderStatusName::CANCELLED]);
		}
		$externalOrderNumberType = $this->configuration['externalOrderNumberType'];

		$entitiesToFlush = [];
		foreach ($orders as $order) {
			$erpId = $order->externalOrderNumber->filter(function ($number) use ($externalOrderNumberType) {
				return $number->type == $externalOrderNumberType;
			});
			if (count($erpId)) {
				$erpId = $erpId->first()->externalOrder;
			} else {
				$erpId = null;
			}
			$orderId = $actionEuropeFeedRepository->getOrderIdWithFeedCount($order->id);
			$actionsDictionary = [
				ActionEuropeNotificationEventType::AE_AWAITING_FULFILLMENT => 'Fulfill',
				ActionEuropeNotificationEventType::AE_CANCELLED            => 'Cancel',
			];
			$mailData = [
				'orderId' => $orderId,
				'erpId'   => $erpId,
				'action'  => $actionsDictionary[$notificationType],
			];

			$result = \Mail\Model\MailManager::sendMail($this->configuration['responsiblePersonEmail'], $mailData, 'Mail\ActionEuropeNotification');
			if ($result !== false) {
				$actionEuropeOrder = $order->actionEuropeStatus;
				if ($notificationType == ActionEuropeNotificationEventType::AE_AWAITING_FULFILLMENT) {
					$actionEuropeOrder->status = ActionEuropeOrderStatusType::AE_ORDER_STATUS_FULFILLMENT_REQUEST_SENT;
					$entityManager->persist($order);
					$entitiesToFlush[] = $order;
				} else {
					$actionEuropeOrder->status = ActionEuropeOrderStatusType::AE_ORDER_STATUS_CANCELLED;
				}
				$entitiesToFlush[] = $actionEuropeOrder;
				$entityManager->persist($actionEuropeOrder);
			} else {
				MccSimpleLogger::log('ActionEurope\mailNotification', ['orderId' => $orderId, 'msg' => 'Order mail cannot be sent']);
			}
		}
		if (!empty($entitiesToFlush)) {
			$entityManager->flush($entitiesToFlush);
		}
		return [
			'total' => count($orders),

		];
	}

	/**
	 * handles the invoice XML
	 * @param Order $order
	 * @param string $invoiceXml a xml string
	 * @return bool true on success
	 */
	public function handleInvoice($order, $invoiceXml)
	{
		$oracleMethodRepository = $this->getOracleMethodRepository();
		$companyName = $order->idChannel->company->name;
		$initArray = $this->extractInvoiceData($order, $invoiceXml);
		$model = new \OracleIntegration\Model\ActionEU\ActionEUInvoiceModel($initArray);
		$oracleMethodRepository->executeMethod('ZAMOWIENIA.BUY_AND_REALIZE_ORDER_AND_WZ', null, $companyName, [':p_xml' => $model]);
		return true;

	}

	/**
	 * Change orders reserved configured minutes ago to confirmed
	 * @return array
	 */
	public function confirmPendingStatuses()
	{
		$config = $this->configuration;
		$orders = $this->getActionEuropeOrderRepository()->findOrdersForReservationConfirmation($config['orderStatuses'][ConfigurationOrderStatusName::AWAITING_CONFIRMATION], $config['reservationConfirmationDelayMinutes']);
		$entitiesToFlush = [];
		foreach ($orders as $order) {
			$order->idOrderMainStatus = $this->getOrderMainStatusRepository()->getStatusByNumber($config['orderStatuses'][ConfigurationOrderStatusName::CONFIRMED]);
			$order->actionEuropeStatus->status = ActionEuropeOrderStatusType::AE_ORDER_STATUS_RESERVATION_SUCCESSFUL;
			$this->entityManager->persist($order);
			$entitiesToFlush[] = $order;
			$entitiesToFlush[] = $order->actionEuropeStatus;
		}
		$this->entityManager->flush($entitiesToFlush);
		return [
			'total' => count($orders),
		];
	}

	/**
	 * Checks if given security hash is correct
	 * @param string $hash the security hash
	 * @return bool whether it is correct
	 */
	public function checkHash($hash)
	{
		return ($hash === $this->configuration['securityHash']);
	}


	/**
	 * Processes the order response and change it's status by a stored feed
	 * @param int $feedId the id of the feed to be processed
	 */
	public function processOrderNotification($feedId)
	{
		MccSystem::runConsoleCommand("action europe process order feed $feedId");
	}

	/**
	 * Retrieve an instance of oracleMethodRepository and initiate it if it does not exist
	 * @return \Document\Repository\OracleMethodRepository
	 */
	protected function getOracleMethodRepository()
	{
		if (!isset($this->oracleMethodRepository)) {
			$this->oracleMethodRepository = new \Document\Repository\OracleMethodRepository($this->entityManager);
		}
		return $this->oracleMethodRepository;
	}

	/**
	 * @param $order
	 * @param $invoiceXml
	 * @return array
	 */
	private function extractInvoiceData($order, $invoiceXml)
	{
		/** @var \Webservice\Repository\ActionEuropeOrderRepository $actionEuropeOrderRepository */
		$actionEuropeOrderRepository = $this->entityManager->getRepository('Webservice\Entity\ActionEuropeOrder');
		$invoiceXml = str_replace('bmecat:', '', $invoiceXml);
		$xmlElement = new MccXmlWrapper($invoiceXml, [
			'x' => 'http://www.opentrans.org/XMLSchema/2.1',
//			'y' => 'http://www.bmecat.org/bmecat/2005',
		]);
		$invoiceNumber = $xmlElement->xpathSingle('//x:INVOICE_ID');
		$paymentDate = $xmlElement->xpathSingle('//x:INVOICE_DATE');
		$actionEuropeCodes = $xmlElement->xpath('//x:PRODUCT_ID/x:SUPPLIER_PID', MccXmlWrapper::XML_TYPE_STRING);
		$codes = $actionEuropeOrderRepository->getProductCodes($order->id, $this->configuration['warehouseId'], $this->configuration['codeTypeId'], $actionEuropeCodes);
		$codes = array_column($codes, 'erpId', 'code');
		$positionsXmlElement = $xmlElement->xpath('//x:INVOICE_ITEM');
		$positions = [];
		/** @var MccXmlWrapper $xmlPosition */
		foreach ($positionsXmlElement as $xmlPosition) {
			$positionResult = [
				'Amount'  => $xmlPosition->xpathSingle('.//x:QUANTITY', MccXmlWrapper::XML_TYPE_INT),
				'Price'   => $xmlPosition->xpathSingle('.//x:PRICE_AMOUNT', MccXmlWrapper::XML_TYPE_DOUBLE),
				'Code'    => $codes[$xmlPosition->xpathSingle('.//x:SUPPLIER_PID')],
				'TaxCode' => round($xmlPosition->xpathSingle('.//x:TAX', MccXmlWrapper::XML_TYPE_DOUBLE) * 100, 2),
			];
			$positions[] = $positionResult;
		}
		$shippingTax = array_reduce($positions, function ($max, $pos) {
			return max($max, $pos['TaxCode']);
		}, 0);

		$servicePositions = [
			[
				'Code'    => 'TRANSP',
				'Amount'  => 1,
				'Price'   => $xmlElement->xpathSingle('//x:ALLOW_OR_CHARGE[./x:ALLOW_OR_CHARGE_TYPE/text()=\'freight\']/x:ALLOW_OR_CHARGE_VALUE/x:AOC_MONETARY_AMOUNT', MccXmlWrapper::XML_TYPE_DOUBLE),
				'TaxCode' => $shippingTax,
			],
		];
		$paymentDate = new \DateTime($paymentDate);
		$paymentDays = $this->configuration['invoicePaymentDays'];
		$dateFormat = $this->configuration['invoiceDateFormat'];
		return [
			'OrderNumber'      => $order->impulsOrderNo,
			'InvoiceNumber'    => $invoiceNumber,
			'PaymentTerm'      => $paymentDate->add(new \DateInterval(sprintf('P%dD', $paymentDays)))->format($dateFormat),
			'Positions'        => ['Position' => $positions],
			'ServicePositions' => ['Position' => $servicePositions],
		];
	}

}