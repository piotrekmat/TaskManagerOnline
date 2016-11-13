<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature\RowGatewayFeature;
use \Application\Model\Adapter as Adapter;
use \Application\Model\Entity\Row;

class Table extends TableGateway
{

    protected $_adapter;

    protected $_primary = null;

    protected $_foreign = null;

    protected $_row = null;

    protected $table = null;

    protected $where = [];

    public function __construct($where = null)
    {
        if (null === $this->table) {
            throw new \Exception("You did not use the name of the table model. " . __CLASS__);
        }

        if (null === $this->_primary) {
            throw new \Exception("You did not use the _primary (key) of the table model. " . __CLASS__);
        }

        if (null === $this->_row) {
            throw new \Exception("You did not use the _row (Class Name) of the row table. " . __CLASS__);
        }

        $this->setWhere($where);

        return parent::__construct($this->table, $this->adapter(), new RowGatewayFeature(new $this->_row($this->_primary, $this->table, $this->adapter())));
    }

    /**
     * 
     * @return Adapter
     */
    public function adapter()
    {

        if (!$this->_adapter) {
            $this->_adapter = Adapter::getInstance();
        }
        return $this->_adapter;
    }

    /**
     * 
     * @return Row
     */
    public function row()
    {
        if (!is_object($this->_row)) {
            $this->_row = new $this->_row();
        }
        return $this->_row;
    }

    public function getPrimary()
    {
        return $this->_primary;
    }

    public function getForeign()
    {
        return $this->_foreign;
    }

    /**
     * @param Where|\Closure|string|array $where
     */
    public function setWhere($where)
    {
        if (is_array($where)) {
            $this->where = array_merge($where, $this->where);
        } elseif (is_numeric($where)) {
            if (is_array($this->_primary))
                throw new \Exception('Klucz główny składa się z tablicy');
            else
                $this->where[$this->_primary] = $where;
        }elseif (!is_null($where)) {
            $this->where = $where;
        }

        return $this;
    }

    public function getWhere()
    {
        return $this->where;
    }

    /**
     * @param Where|\Closure|string|array $where
     * @return ResultSet
     */
    public function select($where = null)
    {
        $this->setWhere($where);
        if (is_string($this->_primary) && array_key_exists($this->_primary, $this->where)) {
            return parent::select($this->getWhere())->current();
        }
        return parent::select($this->where);
    }

    public function update($set, $where = null)
    {
        $this->setWhere($where);
        return parent::update($set, $this->getWhere());
    }

    public function delete($where = null)
    {
        $this->setWhere($where);
        return parent::delete($this->getWhere());
    }
}
