<?php

namespace Webservice\Controller;

use Application\MccController;
use Pdf\Model\DocumentManager;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Webservice\Entity\Hash;

class OrderController extends MccController {
	
	public function indexAction() {

	}

	public function getOrderContentsSpecificationAction() {
		$id = $this->params()->fromPost('id');
		$login = $this->params()->fromPost('login');
		$password = $this->params()->fromPost('password');
		$config = $this->getServiceLocator()->get('config');
		if ($id && is_numeric($id) && ($login === $config['webservice']['login']) && ($password === $config['webservice']['password'])) {
			$em = $this->getEntityManager();
			$dm = new DocumentManager($em);
			$specificationPath = $dm->getSpecificationPdf($id);
			if (file_exists($specificationPath)) {
				$hash = $em->getRepository('Webservice\Entity\Hash')->findOneBy(['type' => 'OrderContentsSpecification', 'hashValue' => $id]);
				if (!$hash) {
					$hash = new Hash();
					$hash->type = 'OrderContentsSpecification';
					$hash->hash = $hash->generateHash();
					$hash->hashValue = $id;
					$hash->requestDate = new \DateTime();
					$hash->requestIp = $this->getRequest()->getServer('REMOTE_ADDR');
					$em->persist($hash);
					$em->flush();
				}
				return new JsonModel([
					'status' => 'success',
					'url' => $this->url()->fromRoute('webservice/order', ['action' => 'getOrderContentsSpecificationFile', 'id' => $hash->hash], ['force_canonical' => true]),
				]);
			}
			return new JsonModel([
				'status' => 'error',
				'reason' => 'document not found',
			]);
		} else {
			return new JsonModel([
				'status' => 'error',
				'reason' => 'incorrect credentials',
			]);
		}
	}

	public function getOrderContentsSpecificationFileAction() {
		$id = $this->params()->fromRoute('id');
		$em = $this->getEntityManager();
		$hash = $em->getRepository('Webservice\Entity\Hash')->findOneBy(['type' => 'OrderContentsSpecification', 'hash' => $id]);
		if ($hash) {
			$dm = new DocumentManager($em);
			$specificationPath = $dm->getSpecificationPdf($hash->hashValue);
			if (file_exists($specificationPath)) {
				$specificationContents = file_get_contents($specificationPath);
				$specificationFilename = basename($specificationPath);
				$response = $this->getResponse();
				$response->setContent($specificationContents);
				$response->getHeaders()
					->clearHeaders()
					->addHeaderLine('Content-Type', 'application/octet-stream')
					->addHeaderLine('Content-Disposition', "attachment; filename=\"$specificationFilename\"")
					->addHeaderLine('Accept-Ranges', 'bytes')
					->addHeaderLine('Content-Length', filesize($specificationPath));
				return $this->response;
			} else {
				return new JsonModel([
					'status' => 'error',
					'reason' => 'document not found',
				]);
			}
		} else {
			return new JsonModel([
				'status' => 'error',
				'reason' => 'incorrect token',
			]);
		}
	}
}