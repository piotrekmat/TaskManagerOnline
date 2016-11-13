<?php

/**
 * Created by PhpStorm.
 * User: marcin
 * Date: 12/11/2016
 * Time: 18:15
 */

namespace Taskmanager\Webservice;

use \Zend\Soap\Wsdl;

/**
 * Class InputComputer
 * @package Taskmanager\Webservice
 */
class InputComputer extends Wsdl
{

    /**
     * @param string $a
     * @param string $b
     * @return array
     */
    public function test($a, $b)
    {
        return [
            'test1' => $a,
            'test2' => $b
        ];
    }


}