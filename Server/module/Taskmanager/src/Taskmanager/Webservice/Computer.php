<?php

/**
 * Created by PhpStorm.
 * User: marcin
 * Date: 12/11/2016
 * Time: 18:15
 */

namespace Taskmanager\Webservice;

/**
 * Class InputComputer
 * @package Taskmanager\Webservice
 */

use \Taskmanager\Model;

class Computer
{
    /**
     * Add information about Computer
     * @param array $data
     * @return bool
     */
    public function addInformation($data = [])
    {


        try {
            $valueArray = get_object_vars($data);
//            $model = new Model\TaskmanagerTable();
//            $row = $model->row();
//            $row->id_computer = $idComputer;
//            $row->json = json_encode($data);
//            $row->save();
            return true;
        } catch (Exception $e) {
            return false;
        }


    }

    /**
     * Get information about a single computer
     * @param array $data
     * @return array
     */
    public function getInformation($data = [])
    {
        $valueArray = get_object_vars($data);
        return [
            "id-compuetr" => $valueArray,
            'date' => $valueArray
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    public function getList($data)
    {
        return [];
    }


}