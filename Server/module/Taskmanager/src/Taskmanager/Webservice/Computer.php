<?php

namespace Taskmanager\Webservice;

use Application\Model\Adapter;
use \Taskmanager\Model;
use \Zend\Db\Adapter\Driver\Mysqli\Mysqli;
use \Zend\Db\Sql\Expression;
use \Zend\Db\Sql\Platform\Mysql\Mysql;
use \Zend\Db\Sql\Select;

class Computer
{
    /**
     *
     * Dodaje informacje o jednym konkretnym komputerze.
     * Parametrem wskazanego komputera powinien być string, jako zakodowany jso, składający się z następujących pól
     * id : "726d1326-8333-4715-898c-940a4c8a799b" string,
     * computer_name : "MAC" string,
     * user_name : "Marek" string,
     * cpu : 17 int [%],
     * ram_mb_used : 25 int [%],
     * hdd_mb_free : 100268 int[],
     * processes : "chrome" string[],
     * create: date
     * @param  string $data
     * @return bool
     */
    public function addInformation($data)
    {
        try {


            $data = json_decode($data, true);


            $row = new Model\TaskmanagerRow();

            $row->id = $data['id'];
            $row->cpu = $data['cpu'];
            $row->computer_name = $data['computer_name'];
            $row->user_name = $data['user_name'];
            $row->ram_mb_used = $data['ram_mb_used'];
            $row->hdd_mb_free = json_encode($data['hdd_mb_free']);
            $row->processes_count = count(explode(';', $data['processes']));
            $row->processes = json_encode($data['processes']);


            $row->save();

            return true;


        } catch (\Exception $e) {

            return false;
        }


    }

    /**
     * Pobiera informacje o jednym wskazanym komputerze, parametrem jest string json (w przypadku array, c# nie daje sobie rady), informacje zwrotne również zawarte są w json jako string, należy wynik zdekodować.
     * Przykładowe ID: f44dccef-01ae-4bcb-bb51-5bcb29ed534f
     * @param string $id
     * @return string
     */
    public function getInformation($id)
    {
        if (empty($id)) {
            throw new \SoapFault("500", "Brak wymaganych parametrów [id]");
        }

        try {
            $table = new Model\TaskmanagerTable();
            $select = $table->getSql()->select();
            $select->columns([
                'create',
                'id',
                'computer_name',
                'user_name',
                'cpu',
                'processes',
                'ram_mb_used',
                'hdd_mb_free',
                'processes',
                'id_computer'
            ]);
            $select->where(['id' => $id]);
            $rows = $table->selectWith($select);
            $data = $rows->toArray();
            return json_encode($data);


        } catch (\Exception $e) {
//            echo $e->getMessage();
//            echo '<br>';
//            echo $e->getTraceAsString();
//            die;


        }
    }

    /**
     * Krotki opis funkcji getList
     * @return string
     */
    public function getList()
    {
        try {

            $table = new Model\TaskmanagerTable();
            $rows = $table->select(function (Select $select) {
                $select->columns([
                    'id',
                    'computer_name',
                    'cpu_avg' => new Expression('AVG(cpu)'),
                    'ram_avg' => new Expression('AVG(ram_mb_used)'),
                    'process_avg' => new Expression('AVG(processes_count)'),
                    'id_computer' => new Expression('COUNT(id_computer)'),
                ]);
                $select->group(['id', 'computer_name']);
//                echo $select->getSqlString(new \Zend\Db\Adapter\Platform\Mysql());
            });
            $data = $rows->toArray();
            return json_encode($data);


        } catch (\Exception $e) {
//            echo $e->getMessage();
//            echo '<br>';
//            echo $e->getTraceAsString();
//            die;
            throw new \SoapFault("500", "Błąd odczytu danych");
        }

    }


}