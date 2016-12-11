<?php

/**
 * @abstract
 * @category   Model
 * @project    System partnerski SIFT
 * @author     Marcin Związek
 * 
 */

namespace Application\Model;

use \Application\Model\Entity\Table;
use \Application\Model\Entity\Row;
use \Zend\Db\Sql\Select;
use \Zend\Db\Sql\Delete;
use \Zend\Db\Sql\Where;
use \Zend\Db\Sql\Sql;
use \Zend\Db\ResultSet\ResultSet;
use \Zend\Db\Sql\AbstractPreparableSql;
use \Exception as Exception;

class Model
{

    use \Application\Traits\Getter;

    protected $_one;

    protected $_oneRow;

    protected $_aDataOne = [];

    protected $_where;

    protected $_columsOne = ['*'];

    protected $_bDeleteOne = false;

    protected $_joinType = Select::JOIN_LEFT;

    public function __construct($where = null)
    {
        $this->setWhere($where);
        $this->init();
    }

    /**
     * Przechowuje wynik selecta;
     * @var ResultSet 
     */
    protected $_oResultSet = null;

    public function init()
    {
        
    }

    /**
     * Zwraca obiekt
     * @return Table
     */
    public function getOne()
    {
        return $this->_one = $this->get($this->_one);
    }

    public function setOne(Table $_one)
    {
        $this->_one = $_one;
        return $this;
    }

    /**
     * 
     * @return Row
     */
    public function getOneRow()
    {
        return $this->_oneRow;
    }

    public function setOneRow(Row $row)
    {
        $this->_oneRow = $row;
        return $this;
    }

    /**
     * $aData['tableName] => array();
     * @param array $aData
     * @return \Application\Model\Model
     */
    public function setData(array $aData)
    {
        return $this;
    }

    public function save()
    {
        try {
            $this->build();
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    protected function build()
    {
        $this->buildOne();
        $this->saveOne();
    }

    /**
     * 
     * @throws \Exception
     */
    protected function buildOne()
    {
        if (!is_object($this->getOneRow())) {
            if (empty($this->_aDataOne)) {
                throw new \Exception("Brak danych do zapisu");
            }

            $oneKeyPrimary = $this->getOne()->getPrimary();
            $onePrimary = $this->_aDataOne[$oneKeyPrimary];

            if ($onePrimary) {
                $this->_oneRow = $this->getOne()->select($onePrimary);
            } elseif (empty($this->_oneRow)) {
                $this->_oneRow = $this->getOne()->row();
            }

            $this->getOneRow()->setData($this->_aDataOne);
            //$data = $this->getOneRow()->toArray();
        } else {
            /**
              // czy aby na pewno
              if (!empty($this->_aDataOne)) {

              }
             */
        }
    }

    protected function saveOne()
    {
        return $this->getOneRow()->save();
    }

    /**
     * Przeniesienie selecta z TableGateway do Modelu.
     * p
     * @param type $where
     * @return Row
     */
    public function select($where = null)
    {
        if (!$this->_oResultSet) {
            $select = $this->sqlSelect($where);
            $result = $this->getOne()->selectWith($select);
            $result->buffer();
            $this->_oResultSet = $result;
        }
        return $this->_oResultSet;
    }

    /**
      public function delete($where = null)
      {
      $delete = $this->sqlDelete($where);
      $result = Adapter::getInstance()->query($delete, Adapter::QUERY_MODE_EXECUTE)->execute();

      return $result;
      }
     */
    public function join(AbstractPreparableSql $sql)
    {
        return $sql;
    }

    /**
     * 
     * @param type $where
     * @return Select
     */
    public function sqlSelect($where = null)
    {
        if ($where instanceof Select) {
            $select = $where;
        } else {
            $select = $this->getOne()->getSql()->select();
            if ($where) {
                $select->where($where);
            }
        }

        $select = $this->join($select);

        return $select;
    }

    public function sqlDelete($where = null)
    {
        if ($where instanceof Delete) {
            $delete = $where;
        } else {
            $delete = $this->getOne()->getSql()->select();
            if ($where) {
                $delete->where($where);
            }
        }

        $delete = $this->join($delete);

        $string = $delete->getSqlString(new \Zend\Db\Adapter\Platform\Mysql());
        return $string = str_replace("SELECT", 'DELETE', $string);
    }

    protected function undetele()
    {
        try {
            if (
                ($this->_bDeleteOne === true ) && ( is_object($this->getOneRow()) === true) && ($this->getOneRow()->rowExistsInDatabase() === true )
            ) {
                $this->getOneRow()->delete();
            }
        } catch (Exception $ex) {
            /**
             * @todo Krytyczny błąd, podczas cofania zmian.
             */
            var_dump("Krytyczny błąd poczas cofania zmian.");
            throw $ex;
        }
    }

    /**
     * 
     * @param boolean $bool
     */
    public function _deleteOneWithError($bool)
    {
        $this->_bDeleteOne = $bool;
    }

    public function getWhere()
    {
        return $this->_where;
    }

    public function setWhere($where)
    {
        $this->_where = $where;
    }

    /**
     * Zwraca nazwy kolumn wykorzystane w Select Modelu (wszystkich joinów) itp.
     * Musi zostać wykonany select, aby wartość nie była null;
     * @return array
     */
    public function getColumns()
    {
        if (is_object($this->_oResultSet)) {
            if ($this->_oResultSet->count()) {
                return array_keys($this->_oResultSet->toArray()[0]);
            }
        }
        return null;
    }
}
