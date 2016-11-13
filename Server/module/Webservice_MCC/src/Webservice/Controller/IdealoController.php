<?php

namespace Webservice\Controller;

use Application\MccController;
use Webservice\Services\Idealo;
use Zend\View\Model\JsonModel;

class IdealoController extends MccController {

	private $idealo;
	public function __construct(Idealo $idealo)
	{
		$this->idealo = $idealo;
	}

	public function indexAction()
	{

	}

	public function prepareFileAction()
	{
		$json = $this->idealo->getProductsJson();

		$save = $this->params()->fromRoute('save', false);

		if (!$save) {
			return new JsonModel($json);
		}


		$dir = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/public/idealo';
		$file = $dir . '/products.json';
		if (!is_dir($dir)) {
			mkdir($dir, 0777, true);
		}

		file_put_contents($file, json_encode($json));

		return false;
	}
}