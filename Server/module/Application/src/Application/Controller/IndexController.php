<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

class IndexController extends AbstractController
{

    public function indexAction()
    {

        //$oNav = new \Application\Navigation\Module();
//        $config = (new \Application\Module())->getConfig()['navigation'];
//        $navigation  = new \Zend\Navigation\Navigation($config);
//        var_dump($navigation);
//        $oView = new \Zend\View\Helper\Navigation();
//        
//        $oView->setContainer($oNav);
//        
//        $this->view()->navigate = $oView;

        $this->redirect()->toUrl('/soap/taskmanager/computer?wsdl');

        return $this->view();
    }
}
