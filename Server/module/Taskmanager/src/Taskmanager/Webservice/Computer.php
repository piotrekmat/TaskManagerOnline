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

use \Taskmanager\Model;

class Computer
{
    /**
     * @param string $idComputer
     * @param array $data
     * @return bool
     */
    public function addInformation($idComputer, $data)
    {
        $model = new Model\TaskmanagerTable();
        $row = $model->row();
        $row->id_computer = $idComputer;
        $row->json = json_encode($data);
        $row->save();

        return true;
    }

//    /**
//     * @param $idComputer
//     * @param $data
//     * @return array
//     */
//    public function getInformation($idComputer, $data)
//    {
//        return [];
//    }
//
//    /**
//     * @return array
//     */
//    public function getList()
//    {
//        return [];
//    }


}