<?php

namespace Taskmanager\Webservice;

use \Taskmanager\Model;

class Computer
{
    /**
     * Opis taki jak ma być, a tablicę podaj w parametrze.
     * @param  string $data
     * @return string
     */
    public function addInformation($data)
    {
        try {

            $model = new Model\TaskmanagerTable();
            $row = $model->row();
            $row->id_computer = $idComputer;
            $row->json = json_encode($data);
            $row->save();
            return "asdasdasdasdas";
        } catch (Exception $e) {
            return false;
        }


    }

    /**
     * Pobiera informacje ostatniego klienta
     * @param string $data
     * @return boolean
     */
    public function getInformation($data)
    {
        $valueArray = get_object_vars($data);
        return [
            "id-compuetr" => "asdasd",
            'date' => "jest ok"
        ];
    }

    /**
     * Krotki opis funkcji getList
     * @return string
     */
    public function getList()
    {
        return 33;
    }


}