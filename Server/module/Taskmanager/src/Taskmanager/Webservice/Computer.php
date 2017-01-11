<?php

namespace Taskmanager\Webservice;

/**
 * Class Computer
 * @class Computer
 */

use \Taskmanager\Model;

class Computer
{
    /**
     * Opis taki jak ma być, a tablicę podaj w parametrze.
     * @param  string
     * @return bool
     */
    public function addInformation($data)
    {


        try {
            $valueArray = get_object_vars($data);
//            $model = new Model\TaskmanagerTable();
//            $row = $model->row();
//            $row->id_computer = $idComputer;
//            $row->json = json_encode($data);
//            $row->save();
            return "asdasdasdasdas";
        } catch (Exception $e) {
            return false;
        }


    }

    /**
     * Krotki opis funkcji
     * @return bool
     */
    public function getInformation()
    {
        $valueArray = get_object_vars($data);
        return [
            "id-compuetr" => $valueArray,
            'date' => $valueArray
        ];
    }

    /**
     * Krotki opis funkcji getList
     * @return string
     */
    public function getList()
    {
        return [];
    }


}