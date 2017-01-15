<?php
/**
 * Created by PhpStorm.
 * User: marcin
 * Date: 13/11/2016
 * Time: 11:45
 */

namespace Taskmanager\Model;

use \Application\Model\Entity\Row;

class TaskmanagerRow extends Row
{

    protected $_primary = 'id_computer';

    protected $table = 'computers';

    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

//    public $id_computer;
//
//    public $id;
//
//    public $computer_name;
//
//    public $user_name;
//
//    public $cpu;
//
//    public $ram_mb_free;
//
//    public $hdd_mb_free;
//
//    public $processes;
//
//    public $processes_count;
//
//    public $create;


}