<?php

namespace Taskmanager\Webservice;

use \Taskmanager\Model;
use Zend\Db\Adapter\Driver\Mysqli\Mysqli;

class Computer
{
    /**
     *
     * Dodaje informacje o jednym konkretnym komputerze.
     * Parametrem wskazanego komputera powinien być string, jako zakodowany jso, składający się z następujących pól
     * id : "726d1326-8333-4715-898c-940a4c8a799b" string,
     * computer_name : "MAC" string,
     * user_name : "Marek" string,
     * cpu : 17 int,
     * ram_mb_free : 3461 int,
     * hdd_mb_free : 100268 int[],
     * processes : "chrome" string[],
     * create: date
     * @param  array $data
     * @return bool
     */
    public function addInformation($data)
    {
        try {

            $data = json_decode($data);

            $row = new Model\TaskmanagerRow();
            $row->id = $data['id'];
            $row->cpu = $data['cpu'];
            $row->computer_name = $data['computer_name'];
            $row->user_name = $data['user_name'];
            $row->ram_mb_free = $data['ram_mb_free'];
            $row->hdd_mb_free = json_encode($data['hdd_mb_free']);
            $row->processes_count = count($data['processes']);
            $row->processes = json_encode($data['processes']);

            $row->save();

            return true;

        } catch (Exception $e) {
            return false;
        }


    }

    /**
     * Pobiera informacje o jednym wskazanym komputerze, parametrem jest string json (w przypadku array, c# nie daje sobie rady), informacje zwrotne również zawarte są w json jako string, należy wynik zdekodować.
     * @param array $params
     * @return string
     */
    public function getInformation($params)
    {
//        $params = json_decode($params);

        if (!isset($params['id']) || empty($params['id'])) {
            throw new \SoapFault("500", "Brak wymaganych parametrów [id]");
        }

        try {

            $id = $params['id'];
            $table = new Model\TaskmanagerTable();
            $select = $table->getSql()->select();
            $select->where(['id' => $id])->limit(100);
            $row = $table->select($select);
            $data = $row->toArray();

            return json_encode($data);


        } catch (\Exception $e) {
            throw new \SoapFault("500", "Błąd odczytu danych");
        }
    }

    /**
     * Krotki opis funkcji getList
     * @return string
     */
    public function getList()
    {
        try {
            $model = new Model\TaskmanagerTable();
            $select = $model->getSql()->select();
            $select->columns(['create', 'id'], false);
            $select->group(['id', 'computer_name']);
            echo $select->getSqlString();
            die;
            $rows = $model->selectWith($select);


            foreach ($rows as $row) {
                var_dump($row->toArray());
            }


        } catch (\Exception $e) {
            throw new \SoapFault("404", "Brak danych");
        }

    }


}