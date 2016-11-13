<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Model\Entity;

use Application\Model\Validate\Field as FieldValidate;
use Zend\Db\Metadata\Metadata;
use \Zend\Db\Metadata\Object\ColumnObject;
use \Application\Model\Adapter as Adapter;

class Row extends \Zend\Db\RowGateway\RowGateway
{

    /**
     * Nazwa klucza głównego
     * @var string Primaty key 
     */
    protected $_primary;

    /**
     * Nazwa klucza obcego
     * @var string Foreign Key
     */
    protected $_foreign;

    /**
     * Nazwa tabeli
     * @var string Tablename
     */
    protected $table;

    /**
     * Schemat nazw kolumn 
     * @var array 
     */
    protected $prototype;

    /**
     * Obiekt z opisem kolumn i ich parametry
     * @var \Zend\Db\Metadata\Metadata 
     */
    protected $metadata;

    protected $_string;

    public function __construct()
    {
        if (null == $this->_primary) {
            throw new \Exception('You did not use name of primary key [$_primary] in row');
        }

        if (null == $this->table) {
            throw new \Exception('You did not use name of table [$table] in row');
        }

        parent::__construct(
            $this->_primary, $this->table, Adapter::getInstance()
        );
    }

    /**
     * 
     * @return \Zend\Db\Metadata\Metadata
     */
    public function metadata()
    {
        if (!$this->metadata) {
            $this->metadata = new Metadata(Adapter::getInstance(), $this->table);
        }

        return $this->metadata;
    }

    /**
     * Zwraca nazwy kolumn tabeli bazy w tablicy.
     * @return array
     */
    public function getPrototype()
    {
        if (!$this->prototype) {
            /* @var $oColumn ColumnObject */
            foreach ($this->metadata()->getColumns($this->table) as $oColumn) {
                $this->prototype[] = $oColumn->getName();
            }
        }
        return $this->prototype;
    }

    /**
     * 
     * @param array $data
     * @return Row
     */
    public function setData(Array $data)
    {

        foreach ($data as $key => $value) {
            $this->$key = $value;
        }

        if ($this->rowExistsInDatabase()) {
            $this->edit = date('Y-m-d H:i:s');
        } else {
            $this->create = date('Y-m-d H:i:s');
        }

        return $this;
    }

    public function __set($name, $value)
    {
        if (in_array($name, $this->getPrototype())) {

            try {
                $oValidate = new FieldValidate();
                $oValidate->setField($this->metadata()->getColumn($name, $this->getTable()));
                $oValidate->isValid($value);
            } catch (Exception $exc) {
                throw new \Exception("Error data input to Row '[$name]'" . $exc->getTraceAsString());
            }

            parent::__set($name, $value);
        }
        return $this;
    }

    public function save()
    {

        if ($this->rowExistsInDatabase()) {
            $this->edit = date("Y-m-d H:i:s");
        }

        $primary = $this->_primary;
        $save = parent::save();

        if (is_array($primary)) {
            return $save;
        }
        return $this->$primary;
    }

    public function delete($where = null)
    {
        /**
         * @todo Ustawić ACL usunięcia (Administrator) jeśli wpis Istnieje i jest duchem.
         */
        if ($this->rowExistsInDatabase() && isset($this->getPrototype()['duch']) && !$this->duch) {
            $this->duch = 1;
            return $this->save();
        } else {
            return parent::delete();
        }
    }

    /**
     * 
     * @return ColumnObject[];
     */
    public function getColumns()
    {
        return $this->metadata()->getColumns($this->table);
    }

    /**
     * 
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->_primary;
    }

    public function getForeignKey()
    {
        return $this->_foreign;
    }

    /**
     * 
     * @return array
     */
    public function toArray($bFullName = false)
    {
        if (true === $bFullName) {
            $return = [];
            foreach ($this->data as $key => $value) {
                $return[$this->getTable() . '_' . $key] = $value;
            }
            return $return;
        }
        return $this->data;
    }

    /**
     * Zwraca nazwę tabeli
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    public function checkProcessPrimaryKeyData()
    {
        if (empty($this->primaryKeyColumn)) {
            return false;
        }
        $this->processPrimaryKeyData();
    }

    public function __toString()
    {
        $data = empty($this->_string) ? $this->_primary : $this->_string;
        return (string) $this->$data;
    }
}
