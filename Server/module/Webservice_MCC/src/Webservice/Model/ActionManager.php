<?php

namespace Webservice\Model;

use Application\MccSystem;
use Doctrine\ORM\EntityManager;
use Price\Entity\ExchangeRate;
use Price\Repository\ExchangeRateRepository;
use Product\Entity\PackageProfile;
use Product\Entity\Product;
use Product\Entity\ProductCode;
use Product\Entity\ProductEan;
use Product\Entity\ProductVersion;
use Product\Entity\ProductVersionDeletedPrice;
use Product\Entity\Translations_Product;
use Warehouse\Repository\CacheRepository;
use ZipArchive;

class ActionManager
{

	protected $entityManager;
	protected $actionClient;
	protected $configuration;


	/**
	 * ActionManager constructor.
	 *
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->configuration = MccSystem::getConfig('IntegrationSettings')['Action'];
	}


	/**
	 * Get SOAP client for Action webservice.
	 *
	 * @return ActionClient
	 */
	protected function getActionClient() {
		if (empty($this->actionClient)) {
			$this->actionClient = new ActionClient($this->entityManager, $this->configuration);
		}
		return $this->actionClient;
	}


	/**
	 * Update current Exchange Rate for Action provider.
	 */
	public function updateExchangeRate() {
		$exchangeRateValue = $this->getActionClient()->getExchangeRate('EUR');
		if ($exchangeRateValue) {

			/** @var ExchangeRateRepository $exchangeRepository */
			$exchangeRepository = $this->entityManager->getRepository('Price\Entity\ExchangeRate');
			$baseCurrency = $this->entityManager->getRepository('Price\Entity\PriceUnit')->findOneByName('EUR');
			$targetCurrency = $this->entityManager->getRepository('Price\Entity\PriceUnit')->findOneByName('PLN');
			$provider = $this->entityManager->getRepository('Price\Entity\ExchangeRateProvider')->findOneByName('Action');

			$exchangeRate = $exchangeRepository->findOneBy(['baseCurrency' => $baseCurrency->id, 'targetCurrency' => $targetCurrency->id, 'provider' => $provider->id, 'date' => new \DateTime()]);
			if (empty($exchangeRate)) {
				$exchangeRate = new ExchangeRate();
				$exchangeRate->provider = $provider;
				$exchangeRate->baseCurrency = $baseCurrency;
				$exchangeRate->targetCurrency = $targetCurrency;
				$exchangeRate->date = new \DateTime();
			}
			$exchangeRate->buyRate = $exchangeRateValue;
			$exchangeRate->sellRate = $exchangeRateValue;
			$this->entityManager->persist($exchangeRate);
			$this->entityManager->flush($exchangeRate);
		}
	}

	/**
	 * Update warehouse stocks.
	 *
	 * @param bool|true $verbose
	 */
	public function updateWarehouseStocks($verbose = true) {
		/** @var CacheRepository $cacheRepository */
		$cacheRepository = $this->entityManager->getRepository('Product\Entity\MagazineDefinition');
		$cacheRepository->setVerbose($verbose);
		$warehouses = $cacheRepository->getWarehouses('Action');

		/** @var ExchangeRateRepository $exchangeRepository */
		$exchangeRepository = $this->entityManager->getRepository('Price\Entity\ExchangeRate');
		$baseCurrency = $this->entityManager->getRepository('Price\Entity\PriceUnit')->findOneByName('EUR');
		$targetCurrency = $this->entityManager->getRepository('Price\Entity\PriceUnit')->findOneByName('PLN');
		$provider = $this->entityManager->getRepository('Price\Entity\ExchangeRateProvider')->findOneByName('Action');
		$exchangeRate = $exchangeRepository->getExchangeRate($baseCurrency, $targetCurrency, new \DateTime(), $provider)['buyRate'];

		$systemProducts = $this->entityManager->createQueryBuilder()
				->select('pv.id', 'pc.code')
				->from('Product\Entity\ProductCode', 'pc', 'pc.code')
				->join('pc.productVersion', 'pv')
				->where('pc.type = :hostProductCode')
				->setParameter('hostProductCode', $this->configuration['productCode'])
				->getQuery()->getArrayResult();

		$productIds = [];
		foreach($systemProducts as $systemProduct) {
			$productIds[$systemProduct['code']][] = $systemProduct['id'];
		}

		$logs = [];
		$actionProducts = $this->getActionClient()->getProducts();
		foreach ($actionProducts as $actionProduct) {
			if (!empty($productIds[$actionProduct->ProductId]) && $actionProduct->Price > 0 && $actionProduct->Quantity > 0) {
				foreach($productIds[$actionProduct->ProductId] as $versionId) {
					$warehouseId = $this->configuration[$actionProduct->ProductStore];
					$warehouses[$warehouseId]['stockDefinitions'][$versionId] = [
							'netStock'      => $actionProduct->Quantity,
							'physicalStock' => $actionProduct->Quantity,
							'sellableStock' => $actionProduct->Quantity,
					];
					$warehouses[$warehouseId]['pricingDefinitions'][$versionId] = round($actionProduct->Price / $exchangeRate, 2);
					if ($this->configuration['enableLogging']) {
						$logs[] = [
								'VersionId'     => $versionId,
								'ProductId'     => $actionProduct->ProductId,
								'Quantity'      => $actionProduct->Quantity,
								'Price'         => $actionProduct->Price,
								'ExchangeRate'  => $exchangeRate,
								'Currency'      => 'EUR',
								'CurrencyPrice' => round($actionProduct->Price / $exchangeRate, 2),
								'Warehouse'     => $actionProduct->ProductStore,
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
	 * Add new products.
	 *
	 * @param bool|true $verbose
	 *
	 * @throws \Doctrine\ORM\ORMException
	 */
	public function addNewProducts($verbose = true) {
		$actionProducts = $this->getActionClient()->getProducts();
		$actionProductEans = $this->getActionClient()->getProductEans();
		$systemProducts = $this->entityManager->createQueryBuilder()
				->select('pc.code')
				->from('Product\Entity\ProductCode', 'pc', 'pc.code')
				->where('pc.type = :hostProductCode')
				->setParameter('hostProductCode', $this->configuration['productCode'])
				->getQuery()->getArrayResult();

		$channels = $this->entityManager->createQueryBuilder()
			->select('ch')
			->from('System\Entity\Channel', 'ch', 'ch.id')
			->join('ch.service', 's')
			->where("s.type <> 'manual'")
			->getQuery()->getResult();

		foreach($actionProducts as $actionProduct) {
			if (empty($systemProducts[$actionProduct->ProductId]) && $actionProduct->Price > 0 && $actionProduct->Quantity > 0 && 'wyp' != strtolower(substr($actionProduct->ProductId, 0, 3))) {
				$product = new Product();
				$product->active = false;
				$product->magazineUnit = $this->entityManager->getReference('Product\Entity\MagazineUnit', 1);
				$product->vatDefinitionType = $this->entityManager->getReference('Order\Entity\VatDefinitionType', 1);

				foreach (['de_DE', 'pl_PL'] as $languageCode) {
					$productTranslation = new Translations_Product;
					$productTranslation->lang = $this->entityManager->getReference('System\Entity\DataLang', $languageCode);
					$productTranslation->product = $product;
					$productTranslation->name = $actionProduct->ProductName;
					$productTranslation->isActive = true;
					$product->translations->add($productTranslation);
					$this->entityManager->persist($productTranslation);
				}

				foreach(['2', '3'] as $companyId) {
					$courierId = ($actionProduct->ProductStore == 'LocalStore') ? 1 : 2;
					$packageProfile = new PackageProfile();
					$packageProfile->company = $this->entityManager->getReference('System\Entity\Company', $companyId);
					$packageProfile->courier = $this->entityManager->getReference('Order\Entity\Courier', $courierId);
					$packageProfile->product = $product;
					$packageProfile->active = true;
					$packageProfile->cashOnDelivery = true;
					$product->packageProfiles->add($packageProfile);
					$this->entityManager->persist($packageProfile);
				}

				$productVersion = new ProductVersion();
				$productVersion->product = $product;
				$productVersion->isNewFromAction = true;
				$product->versions->add($productVersion);

				$productCode = new ProductCode();
				$productCode->code = $actionProduct->ProductId;
				$productCode->productVersion = $productVersion;
				$productCode->type = $this->entityManager->getReference('Product\Entity\ProductCodeType', $this->configuration['productCode']);
				$productVersion->codes->add($productCode);
				$this->entityManager->persist($productCode);


				if (!empty($actionProductEans[$actionProduct->ProductId])) {
					foreach ($actionProductEans[$actionProduct->ProductId] as $productEan) {
						$ean = new ProductEan();
						$ean->productVersion = $productVersion;
						$ean->ean = $productEan;
						$productVersion->eans->add($ean);
						$this->entityManager->persist($ean);
					}
				}

				foreach($channels as $channel) {
					$deletedPrice = new ProductVersionDeletedPrice();
					$deletedPrice->productVersion = $productVersion;
					$deletedPrice->channel = $channel;
					$this->entityManager->persist($deletedPrice);
				}

				$systemProducts[$actionProduct->ProductId] = $actionProduct->ProductId;

				$this->entityManager->persist($product);
				$this->entityManager->persist($productVersion);

				if ($verbose) {
					echo 'Added product: ' . $actionProduct->ProductId . PHP_EOL;
				}
			}
		}

		$this->entityManager->flush();
	}


	/**
	 * Log stock changes.
	 *
	 * @param array $logs
	 *
	 * @return bool
	 */
	public function log($logs) {
		$logPath = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/data/Csv/ActionStock/' . date('Y/m/d/');
		if(!file_exists($logPath)) {
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

}