<?php
/**
 * Created by PhpStorm.
 * User: marcin
 * Date: 13/11/2016
 * Time: 11:43
 */

namespace Taskmanager\Model;

use \Application\Model\Entity\Table;

class TaskmanagerTable extends Table
{
    protected $_primary = 'id_computer';

    protected $_row = '\Taskmanager\Model\TaskmanagerRow';

    protected $table = 'computers';
}