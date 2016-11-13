<?php
/**
 * Created by PhpStorm.
 * User: rafalmnich
 * Date: 14.06.2016
 * Time: 12:04
 */

namespace Webservice\Factory;


use Webservice\Controller\IdealoController;
use Webservice\Services\Idealo;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IdealoControllerFactory implements FactoryInterface
{

	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return mixed
	 */
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$realServiceLocator = $serviceLocator->getServiceLocator();
		$idealoService = $realServiceLocator->get(Idealo::class);

		return new IdealoController($idealoService);
	}
}