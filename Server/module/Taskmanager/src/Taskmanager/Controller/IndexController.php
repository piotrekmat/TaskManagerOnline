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

        $test = new  Computer();
//        $result = $test->getList();
        $data = "f44dccef-01ae-4bcb-bb51-5bcb29ed534f";
        $result = $test->getInformation($data);
        var_dump($result);


        die("KONIEC");
    }


}
