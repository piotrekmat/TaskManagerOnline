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

    protected $_primary = 'id';


    protected $table = 'data';
}