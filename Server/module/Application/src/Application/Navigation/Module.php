<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Navigation;

use Zend\Navigation\Navigation;

/**
 * Description of Module
 *
 * @author marcin
 */
class Module extends Navigation
{

    protected $pages = [
    ];

    public function __construct()
    {

        $config = (new \Application\Module())->getConfig()['navigation'];
        
//        
//        $oServiceManager = \Application\Services\ServiceLocatorFactory::getInstance();
//        /* @var $aConfig \Zend\Mvc\Router\Http\TreeRouteStack */
//        $oConfig = $oServiceManager->get('Router');
//        /* @var $oList \Zend\Mvc\Router\PriorityList */
//        $oList = $oConfig->getRoutes();
//        //var_dump($oList->toArray());
//        $route = $oList->toArray();
//
//        foreach ($route as $key => $value) {
//            
//        }

        parent::__construct($config);
    }
}
