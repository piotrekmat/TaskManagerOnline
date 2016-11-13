<?php

namespace Webservice\Controller;

use Application\MccController;
use Webservice\Model\ActionManager;

class ActionController extends MccController {
	
	public function indexAction() {

	}

	public function updateWarehouseCacheAction() {
		if(!$this->getRequest() instanceof \Zend\Console\Request) {
			return $this->showMessage();
		} else {
			$verbose = $this->params()->fromRoute('verbose', true);
			$entityManager = $this->getEntityManager();
			$actionManager = new ActionManager($entityManager);
			$logger = $this->getLogger();
			$result = $logger->log(
					function() use ($actionManager, $verbose) {
						$actionManager->updateExchangeRate();
						$actionManager->updateWarehouseStocks($verbose);
					},
					[
							'type' => 'Action Manager',
							'subtype' => 'Update Stocks',
							'concurrentAllowed' => false,
							'allowSleep' => false,
					]
			);
			return ($result) ? '1' : '0';
		}
	}

	public function addNewProductsAction() {
		if(!$this->getRequest() instanceof \Zend\Console\Request) {
			return $this->showMessage();
		} else {
			$verbose = $this->params()->fromRoute('verbose', true);
			$entityManager = $this->getEntityManager();
			$actionManager = new ActionManager($entityManager);
			$logger = $this->getLogger();
			$result = $logger->log(
					function() use ($actionManager, $verbose) {
						$actionManager->addNewProducts($verbose);
					},
					[
							'type' => 'Action Manager',
							'subtype' => 'Add Products',
							'concurrentAllowed' => false,
							'allowSleep' => false,
					]
			);
			return ($result) ? '1' : '0';
		}
	}


}