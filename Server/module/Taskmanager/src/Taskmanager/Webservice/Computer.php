<?php

/**
 * Created by PhpStorm.
 * User: marcin
 * Date: 12/11/2016
 * Time: 18:15
 */

namespace Taskmanager\Webservice;

use chobie\Jira\Api\Exception;
use \Zend\Soap\Wsdl;

/**
 * Class InputComputer
 * @package Taskmanager\Webservice
 */

use \Taskmanager\Model;

class Computer
{
    /**
     * Add information about single computer
     * @param string $idComputer
     * @param string $data
     * @return bool
     */
    public function addInformation($idComputer, $data)
    {
        try {
            $model = new Model\TaskmanagerTable();
            $row = $model->row();
            $row->id_computer = $idComputer;
            $row->json = json_encode($data);
            $row->save();

            return true;
        } catch (Exception $e) {
            return false;
        }


    }

    /**
     * Get information about a single computer
     * @param string $idComputer
     * @param string $data
     * @return array
     */
    public function getInformation($idComputer, $data)
    {
        return [
            "idComputer" => $idComputer,
            "data" => $data
        ];
    }

    /**
     * Get list of computers
     * @return array
     */
    public function getList()
    {
        return [];
    }


}