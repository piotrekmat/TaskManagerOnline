<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Model;

use \Application\Model\Entity\Table;
use \Application\Model\Entity\Row;
use \Zend\Db\Sql\Select;
use \Zend\Db\Sql\AbstractPreparableSql;
use \Exception as Exception;

class OneToMany extends Model
{

    protected $_many;

    protected $_manyRow;

    private $_aDataMany = [];

    protected $_columnsMany = ['*'];

    private $_aDataDelete = [];

    private $_bDeleteMany = false;

    /**
     * 
     * @param boolean $bool
     */
    public function _deleteManyWithError($bool)
    {
        $this->_bDeleteMany = $bool;
    }

    /**
     * Zwraca obiekt
     * @return Table
     */
    public function getMany()
    {
        return $this->_many = $this->get($this->_many);
    }

    /**
     * 
     * @param Table $_many
     */
    public function setMany(Table $_many)
    {
        $this->_many = $_many;
    }

    /**
     * 
     * @return Row[]
     */
    public function getManyRow()
    {
        return $this->_manyRow;
    }

    /**
     * 
     * @param Row $row
     * @return \Application\Model\OneToMany
     * @throws Exception
     */
    public function setManyRow($row)
    {
        if ($row instanceof Row) {
            $this->_manyRow[] = $row;
        } elseif (is_array($row)) {
            $this->_manyRow = array_merge($row, $this->_manyRow);
        } else {
            throw new Exception('[' . __CLASS__ . '][' . __METHOD__ . '] Błąd parametru $row');
        }

        return $this;
    }

    /**
     * Data zawiera tablicę asocjacyjną, która zawiera nazwę tabeli relacji One
     * w której to znajdują się dane dla tabli tej relacji. 
     * 
     * W tablicy asocjacyjnej o nazwie tabeli relacji Many znajduje się tablica
     * kolejno danych relacji Many.
     * 
     * WZÓR: 
     * $aData['TABLE_NAME_ONE'] => (array) $data
     * $aData['TABLE_NAME_MANY'] => [
     *      0 => (array) $data
     *      1 => (array) $data
     * ]
     * @param array 
     */
    public function setData(array $aData = [])
    {
        $tableOneName = $this->getOne()->getTable();
        $tableManyName = $this->getMany()->getTable();

        if (array_key_exists($tableOneName, $aData)) {
            $this->_aDataOne = $aData[$tableOneName];
        }

        if (array_key_exists($tableManyName, $aData)) {
            $this->_aDataMany = $aData[$tableManyName];
        }

        return $this;
    }

    protected function build()
    {
        $this->buildOne();
        $this->buildMany();

        $this->saveOne();
        $this->saveMany();

        if ($this->_bDeleteMany) {
            $this->deleteMany($this->_aDataDelete);
        }
    }

    /**
     * @todo do poprawienia
     * @throws \Exception
     */
    protected function buildMany()
    {
        if (empty($this->_aDataMany)) {
            throw new Exception("Brak danych do zapisu");
        }
        $oneKeyPrimary = $this->getOne()->getPrimary();
        $manyKeyPrimary = $this->getMany()->getPrimary();
        $aKluczIstnieje = $aIds = [];

        if ($this->getOneRow()->rowExistsInDatabase()) {
            $rows = $this->getMany()->select([
                $oneKeyPrimary = $this->getOneRow()->$oneKeyPrimary
            ]);

            foreach ($rows as $manyRow) {
                $aIds[] = $manyRow->$manyKeyPrimary;
            }
        }

        foreach ($this->_aDataMany as $aDataMany) {
            if (!in_array($aDataMany[$manyKeyPrimary], $aIds)) {
                $aDataMany[$manyKeyPrimary] = null;
                $manyRow = $this->getMany()->row();
            } else {
                $manyRow = $this->getMany()->select($aDataMany[$manyKeyPrimary]);
                $aKluczIstnieje[] = $aDataMany[$manyKeyPrimary];
            }
            $this->_manyRow[] = $manyRow->setData($aDataMany);
        }

        $this->_aDataDelete = array_diff($aKluczIstnieje, $aIds);
    }

    protected function saveMany()
    {
        $aId = [];
        $primaryManyKey = $this->getMany()->getPrimary();
        $primaryOneKey = $this->getOne()->getPrimary();
        foreach ($this->getManyRow() as $oManyRow) {
            if (!$primaryOne = $this->getOneRow()->$primaryOneKey) {
                throw new Exception("Brak wartości klucza głównego w relacji (One<=>Many)");
            }
            $oManyRow->$primaryOneKey = $primaryOne;
            $aId[] = $oManyRow->$primaryManyKey = $oManyRow->save();
        }
        return $aId;
    }

    /**
     * Wprowadza tablicę idków many do usunięcia.
     * $aData = [
     *  0 => (int) id,
     *  1 => (int) id
     * ]
     * @param array $aData
     */
    public function deleteMany($aData)
    {
        foreach ($aData as $id) {
            $oRow = $this->getMany()->select($id);
            if ($oRow instanceof Row) {
                $oRow->delete();
            }
        }
    }

    public function join(AbstractPreparableSql $sql)
    {
        $sManyTable = $this->getMany()->getTable();
        $sOneTable = $this->getOne()->getTable();
        $sOnePrimary = $this->getOne()->getPrimary();


        $on = $sOneTable . '.' . $sOnePrimary . '=' . $sManyTable . '.' . $sOnePrimary;
        $sql->join(
            $sManyTable, $on, $this->_columnsMany, $this->_joinType
        );

        return $sql;
    }
    
}
