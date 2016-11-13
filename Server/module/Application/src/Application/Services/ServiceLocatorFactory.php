<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Services;

use \Zend\ServiceManager\ServiceManager;
use \Exception as Exception;

/**
 * Description of ServiceLocatorFactory
 *
 * @author marcin
 */
class ServiceLocatorFactory
{

    private static $serviceManager;

    private function __construct()
    {
        
    }

    /**
     * 
     * @return ServiceManager
     * @throws Exception
     */
    public static function getInstance()
    {
        if (null === self::$serviceManager) {
            throw new Exception('ServiceLocator is not set');
        }
        
        return self::$serviceManager;
    }

    /**
     * 
     * @param ServiceManager $sm
     */
    public static function setInstance(ServiceManager $sm)
    {
        self::$serviceManager = $sm;
    }
}
