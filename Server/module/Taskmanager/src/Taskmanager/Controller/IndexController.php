<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Taskmanager\Controller;

use \Application\Controller\AbstractController;
use \Taskmanager\Webservice\Computer;


class IndexController extends AbstractController
{

    public function indexAction()
    {
        try {
            $test = new  Computer();
            $test->getList();
        } catch (\Exception $e) {
            echo print_r($e->getTraceAsString());
        }
        die('poszlo');
    }


}
