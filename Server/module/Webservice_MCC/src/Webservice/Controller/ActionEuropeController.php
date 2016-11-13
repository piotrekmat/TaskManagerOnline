<?php

namespace Webservice\Controller;

use Application\MccController;
use Webservice\Enums\ActionEuropeNotificationEventType;
use Webservice\Enums\ConfigurationOrderStatusName;
use Webservice\Model\ActionEuropeManager;


class ActionEuropeController extends MccController {

	/**
	 * @var ActionEuropeManager
	 */
	protected $actionEuropeManager;
	
	public function indexAction() {

	}

	/**
	 * Test console action for testing purposes
	 * routing:
	 * action europe test [--verbose=]
	 * @return \Zend\View\Model\ViewModel
	 */
	public function testAction() {
		if(!$this->getRequest() instanceof \Zend\Console\Request) {
			return $this->showMessage();
		} else {
			$config = $this->getServiceLocator()->get('config');
			$config = $config['IntegrationSettings']['ActionEurope'];
			/** @var \Webservice\Repository\ActionEuropeOrderRepository  $actionEuropeOrderRepository */
			$actionEuropeOrderRepository = $this->getEntityManager()->getRepository('Webservice\Entity\ActionEuropeOrder');
			$result = $actionEuropeOrderRepository->findOrdersForReservationConfirmation($config['orderStatuses'][ConfigurationOrderStatusName::AWAITING_CONFIRMATION],15);
//			print_r($result);
			print_r(array_map(function ($order) {
				return $order->id;
			}, $result));
			exit;
			$invoiceData =
				[
					'OrderNumber'      => 'BAE/43/000001/2016',
					'InvoiceNumber'    => 'FV/1234567890A',
					'PaymentTerm'      => '29.02.2016',
					'Positions'        => [
						'Position' => [
							[
								'Code'    => '209029',
								'Amount'  => 2,
								'Price'   => 99.9,
								'TaxCode' => 0,
							],
							[
								'Code'    => '109028',
								'Amount'  => 2,
								'Price'   => 21.1,
								'TaxCode' => 0,
							],
						],
					],
					'ServicePositions' => [
						'Position' => [
							[
								'Code'    => 'TRANSP',
								'Amount'  => 1,
								'Price'   => 55.5,
								'TaxCode' => 0,
							],
						],
					],
				];
			$model = new \OracleIntegration\Model\ActionEU\ActionEUInvoiceModel($invoiceData);
//			$model->Positions->Position[0]->
//			echo $model->asXml('params'); exit;
			$actionManager = $this->getActionEuropeManager();
//			$result = $actionManager->reserveOrders();



			$codes = $actionEuropeOrderRepository->getProductCodes(985096,47,11, ['AC000006']);
			$codes = array_column($codes, 'erpId', 'code');
			print_r($codes); exit;
			$entityManager = $this->getEntityManager();
			/** @var \Order\Repository\OrderRepository $orderRepository */
			$orderRepository = $entityManager->getRepository('Order\Entity\Order');
			$order = $orderRepository->find(983948);
			$orderModel = new \Webservice\Model\ActionEuropeOrderModel($order, $config);
			$serializer = new \XML_Serializer([
				XML_SERIALIZER_OPTION_INDENT        => '    ',
				XML_SERIALIZER_OPTION_RETURN_RESULT => true,
				XML_SERIALIZER_OPTION_ROOT_NAME => 'params',
				XML_SERIALIZER_OPTION_XML_DECL_ENABLED => 'true',
				XML_SERIALIZER_OPTION_CLASSNAME_AS_TAGNAME => 'true',
				XML_SERIALIZER_OPTION_TAGMAP=>['Webservice\Model\ActionEuropeOrderItemModel'=>'oItem']

			]);
			$result = $serializer->serialize($orderModel);
			echo $result; exit;
//			$orderRepository->
//			$orderRepository
////			$xml = file_get_contents('/home/kudlaty01/Dokumenty/Action/EU/examples/20160218/DeliveryNote 138363.xml');
//			$xmlOrder = file_get_contents('/home/kudlaty01/Dokumenty/Action/EU/examples/20160218/OrderResponse 48638.xml');
//			$xmlElement = new \SimpleXMLElement($xmlOrder);
//			$xmlElement->registerXPathNamespace('x', 'http://www.opentrans.org/XMLSchema/2.1');
////			$number = $xmlElement->xpath('*')[0]->xpath('*');
////			$number = $xmlElement->xpath('//x:ORDER_ID')[0];
//			$number = $xmlElement->xpath('//x:SUPPLIER_ORDER_ID');
//			print_r(count($number));
////			print_r((int)$number);
////			echo $xml;
//			exit;
			print_r($result);exit;
			print_r($config['orderStatuses'][ConfigurationOrderStatusName::PENDING_RESERVATION]);
			$orders = $actionEuropeOrderRepository->findOrdersToProcess($config['warehouseId'],$config['orderStatuses'][ConfigurationOrderStatusName::PENDING_RESERVATION]);
			echo gettype($orders) . ' ' . count($orders);
			exit;
			$output=$actionManager->reserveOrder($order);
			print_r($output);
//			$actionManager->updateWarehouseStocks(true);
		}
		return '1';
	}

	/**
	 * routing:
	 * action europe update warehouse cache [--verbose=]
	 * @return string|\Zend\View\Model\ViewModel
	 */
	public function updateActionEuropeWarehouseCacheAction()
	{
		if (!$this->getRequest() instanceof \Zend\Console\Request) {
			return $this->showMessage();
		} else {
			$verbose = $this->params()->fromRoute('verbose', true);
			$actionEuropeManager = $this->getActionEuropeManager();
			$logger = $this->getLogger();
			$result = $logger->log(
				function () use ($actionEuropeManager, $verbose) {
					$actionEuropeManager->updateWarehouseStocks($verbose);
				},
				[
					'type' => 'Action Europe Manager',
					'subtype' => 'Update Stocks',
					'concurrentAllowed' => false,
					'allowSleep' => false,
				]
			);
			return ($result) ? '1' : '0';
		}
	}

	public function updateWarehouseCacheAction() {
		if(!$this->getRequest() instanceof \Zend\Console\Request) {
			return $this->showMessage();
		} else {
			$verbose = $this->params()->fromRoute('verbose', true);
			$actionManager = $this->getActionEuropeManager();
			$logger = $this->getLogger();
			$result = $logger->log(
					function() use ($actionManager, $verbose) {
						$actionManager->updateWarehouseStocks($verbose);
					},
					[
							'type' => 'Action Europe Manager',
							'subtype' => 'Update Stocks',
							'concurrentAllowed' => false,
							'allowSleep' => false,
					]
			);
			return ($result) ? '1' : '0';
		}
	}

	/**
	 * Action for ActionEurope to send their XML messages
	 * NOT a Console Action
	 * /webservice/action-europe/notificationGateway
	 * @return \Zend\Http\Response
	 */
	public function notificationGatewayAction()
	{
		/** @var \Zend\Http\Request $request */
		$request = $this->getRequest();
		if (!$request instanceof \Zend\Http\Request) {
			return $this->showMessage();
		} else {
			/** @var \Zend\Http\Response $response */
			$response = $this->getResponse();
			$hash = $this->params('id');
			$actionManager = $this->getActionEuropeManager();
			if (is_string($hash) && $actionManager->checkHash($hash)) {
				$rawXml = $request->getContent();
				$feed = $actionManager->createXmlFeed($rawXml);
				if (FALSE !== $feed) {
					$actionManager->processOrderNotification($feed->id);
				}
				$response->setStatusCode(200);
			} else {
				$response->setStatusCode(403);
			}
			return $response;
		}
	}

	/**
	 * Action for asynchronous order feed processing
	 * routing:
	 * action europe process order feed <feedId>
	 * @throws \Exception
	 */
	public function processOrderFeedAction()
	{
		if (!$this->getRequest() instanceof \Zend\Console\Request) {
			return $this->showMessage();
		} else {
			$feedId = $this->params('feedId');
			if (!$feedId) {
				return '0';
			}
			$actionManager = $this->getActionEuropeManager();
			$actionManager->processOrderFeed($feedId);
			return '1';
		}
	}

	/**
	 * Action for reserving multiple orders in Action Europe
	 * routing:
	 * action europe reserve orders
	 * @return string 0 or 1
	 */
	public function reserveOrdersAction()
	{
		if (!$this->getRequest() instanceof \Zend\Console\Request) {
			return $this->showMessage();
		} else {
			$actionManager = $this->getActionEuropeManager();
			$logger = $this->getLogger();
			$result = $logger->log(
				function () use ($actionManager) {
					$result= $actionManager->reserveOrders();
					return [
						'valueA' => $result['total'],
						'valueB' => $result['successful'],
					];
				},
				[
					'type'              => 'Action Europe Manager',
					'subtype'           => 'Orders reservation',
					'concurrentAllowed' => false,
					'concurrentOperations' =>
						[

							[
								'type'    => 'Action Europe Manager',
								'subtype' => 'Orders actions performance',
							],
						],
					'allowSleep'        => false,
				]
			);
			return ($result) ? '1' : '0';
		}
	}

	/**
	 * Action that sends mail notifications about orders being confirmed
	 * routing:
	 * action europe send reservation mail
	 * @return string
	 */
	public function sendReservationMailAction()
	{
		if (!$this->getRequest() instanceof \Zend\Console\Request) {
			return $this->showMessage();
		} else {
			$actionManager = $this->getActionEuropeManager();
			$logger = $this->getLogger();
			$result = $logger->log(
				function () use ($actionManager) {
					$result = $actionManager->notifyByMail(ActionEuropeNotificationEventType::AE_AWAITING_FULFILLMENT);
					return [
						'valueA' => $result['total'],
					];
				},
				[
					'type'              => 'Action Europe Manager',
					'subtype'           => 'Reservation mail notification',
					'concurrentAllowed' => false,
					'concurrentOperations' =>
						[

							[
								'type'    => 'Action Europe Manager',
								'subtype' => 'Orders actions performance',
							],
						],
					'allowSleep'        => false,
				]
			);
			return ($result) ? '1' : '0';
		}
	}

	/**
	 * Action finding cancelled orders associated with ActionEurope and mailing about it
	 * routing:
	 * action europe send cancellation mail
	 * @return string
	 */
	public function sendCancellationMailAction()
	{
		if (!$this->getRequest() instanceof \Zend\Console\Request) {
			return $this->showMessage();
		} else {
			$actionManager = $this->getActionEuropeManager();
			$logger = $this->getLogger();
			$result = $logger->log(
				function () use ($actionManager) {
					$result = $actionManager->notifyByMail(ActionEuropeNotificationEventType::AE_CANCELLED);
					return [
						'valueA' => $result['total'],
					];
				},
				[
					'type'                 => 'Action Europe Manager',
					'subtype'              => 'Cancellation mail notification',
					'concurrentAllowed'    => false,
					'concurrentOperations' =>
						[

							[
								'type'    => 'Action Europe Manager',
								'subtype' => 'Orders actions performance',
							],
						],
					'allowSleep'           => false,
				]
			);
			return ($result) ? '1' : '0';
		}
	}

	/**
	 *  Main action for all ActionEurope operations
	 * route:
	 * action europe process orders
	 */
	public function processActionEuropeOrdersAction()
	{
		if (!$this->getRequest() instanceof \Zend\Console\Request) {
			return $this->showMessage();
		} else {
			$actionManager = $this->getActionEuropeManager();
			$logger = $this->getLogger();
			$result = $logger->log(
				function () use ($actionManager) {
					$reserveResult = $actionManager->reserveOrders();
					$confirmationResult = $actionManager->confirmPendingStatuses();
					$fulfillmentNotificationResult = $actionManager->notifyByMail(ActionEuropeNotificationEventType::AE_AWAITING_FULFILLMENT);
					$cancellationNotificationResult = $actionManager->notifyByMail(ActionEuropeNotificationEventType::AE_CANCELLED);
					return [
						'valueA' => $reserveResult['total'],
						'valueB' => $reserveResult['successful'],
						'valueC' => $confirmationResult['total'],
						'valueD' => $fulfillmentNotificationResult['total'],
						'valueE' => $cancellationNotificationResult['total'],
					];
				},
				[
					'type'                 => 'Action Europe Manager',
					'subtype'              => 'Orders actions performance',
					'concurrentAllowed'    => false,
					'concurrentOperations' =>
						[

							[
								'type'    => 'Action Europe Manager',
								'subtype' => 'Orders reservation',
							],
							[
								'type'    => 'Action Europe Manager',
								'subtype' => 'Confirm reserved orders',
							],
							[
								'type'    => 'Action Europe Manager',
								'subtype' => 'Reservation mail notification',
							],
							[
								'type'    => 'Action Europe Manager',
								'subtype' => 'Cancellation mail notification',
							],
						],
					'allowSleep'           => false,
				]);
			return ($result) ? '1' : '0';
		}
	}

	/**
	 * Confirm orders reserved some time ago
	 * routing:
	 * action europe confirm reserved orders
	 * @return string
	 */
	public function confirmReservedOrdersAction()
	{
		if (!$this->getRequest() instanceof \Zend\Console\Request) {
			return $this->showMessage();
		} else {
			$actionManager = $this->getActionEuropeManager();
			$logger = $this->getLogger();
			$result = $logger->log(
				function () use ($actionManager) {
					$result = $this->confirmReservedOrdersAction();
					return [
						'valueA' => $result['total'],
					];
				},
				[
					'type'                 => 'Action Europe Manager',
					'subtype'              => 'Confirm reserved orders',
					'concurrentAllowed'    => false,
					'concurrentOperations' =>
						[

							[
								'type'    => 'Action Europe Manager',
								'subtype' => 'Orders actions performance',
							],
						],
					'allowSleep'           => false,
				]
			);
			return ($result) ? '1' : '0';
		}
	}

	/**
	 * Get an instance of actionEuropeManager or create one if it does not exist 
	 * @return ActionEuropeManager
	 */
	private function getActionEuropeManager()
	{
		if (!$this->actionEuropeManager) {
			$this->actionEuropeManager = new ActionEuropeManager($this->getEntityManager());
		}
		return $this->actionEuropeManager;
	}

}