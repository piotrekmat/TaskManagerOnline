<?php
/**
 * Created by PhpStorm.
 * User: marcin
 * Date: 29/10/2016
 * Time: 23:10
 */

namespace Application\Factory;

use Zend\Mvc\Application;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use \Zend\Soap\AutoDiscover;
use \Zend\Soap\Server;

class SoapFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {

        die("wpadło");
            $sl = $serviceLocator->getServiceLocator();
        $route = 'home';
        $wsGenerator = $sl->get('\Zend\Soap\AutoDiscover');
        $soapServer = $sl->get('\Zend\Soap\Server');

        return new \Application\Controller\SoapController($route, $soapServer, $wsGenerator);

    }
}