<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OneToOne
 *
 * @author marcin
 */
use \Application\Model\Entity\Table;
use \Application\Model\Entity\Row;
use \Zend\Db\Sql\AbstractPreparableSql;
use \Exception as Exception;

namespace Application\Model;

class OneToOne extends Model
{

    protected $_second;

    protected $_secondRow;

    protected $_aDataSecond = [];

    protected $_columnsSecond = ['*'];

    protected $_addNew = false;

    /**
     * Zwraca obiekt
     * @return Entity\Table
     */
    public function getSecond()
    {
        return $this->_second = $this->get($this->_second);
    }

    public function setSecond(Table $_second)
    {
        $this->_second = $_second;
    }

    public function setSecondRow(Row $oRow)
    {
        $this->_secondRow = $oRow;
    }

    /**
     * 
     * @return Entity\Row
     */
    public function getSecondRow()
    {
        return $this->_secondRow;
    }

    /**
     * Wykorzystywane w przypadku relacji One to Many, ale przy dodawaniu za każdym razem nowej wartosci many. 
     * Np przy stanie zapamiętania haseł. 
     * Użytkownik zmieniajac hasło sprawdza unikatowość danych
     * jeśli ta się nie powtarza dodaje nowy wiersz. 
     * @param boolean $bool
     */
    public function addNewSecond($bool)
    {
        $this->_addNew = (bool) $bool;
    }

    /**
     * 
     * @param array $aData aData['sOneNameTable' => [data], 'sSecondNameTable' => [data]  ];
     *              
     * @return \Application\Model\OneToOne
     * @throws \Exception
     */
    public function setData(array $aData = [])
    {
        /**
         * @todo Pomyśleć nad rozwiazaniem wyłącznie klucza primary z tabeli One
         */
        $oneNameTable = $this->getOne()->getTable();
        $secondNameTable = $this->getSecond()->getTable();

        if (array_key_exists($secondNameTable, $aData)) {
            $this->_aDataSecond = $aData[$secondNameTable];
        }

        if (is_object($this->getOneRow())) {
            $this->_aDataOne = $this->getOneRow()->toArray();
        } elseif (array_key_exists($oneNameTable, $aData)) {
            $this->_aDataOne = $aData[$oneNameTable];
        }

        return $this;
    }

    protected function setDataCheck($aData)
    {
        /**
         * @todo Może kiedyś, ale wydajnościowo do bani.
         *       Table sprawdza i wprowadza właściwę pole obiektu. To wystarczy.
         * @return boolean | Exception
         */
    }

    protected function build()
    {
        $this->buildOne();
        $this->saveOne();
        $this->buildSecond();
        $this->saveSecond();
    }

    protected function buildSecond()
    {
        if (empty($this->_aDataSecond)) {
            throw new Exception("Brak danych do zapisu [_aDataSecond] in [" . __CLASS__ . "]");
        }

        $oneKeyPrimary = $this->getOne()->getPrimary();
        $onePrimary = null;

        // spradzanie klucza obcego dla Many;
        if ($this->_aDataOne[$oneKeyPrimary]) {
            $onePrimary = $this->_aDataOne[$oneKeyPrimary];
        } elseif (is_object($this->getOneRow()) && $onePrimary = $this->getOneRow()->$oneKeyPrimary) {
            $this->_aDataSecond[$oneKeyPrimary] = $onePrimary;
        }

        // tworzenie obiektu lub pobieranie selectem, jeśli nie ma dodawać nowych wierszy
        if ($onePrimary && $this->_addNew == false) {
            $this->_secondRow = $this->getSecond()->select([
                    $oneKeyPrimary => $onePrimary
                ])->current();
        }

        if (!is_object($this->_secondRow)) {
            $this->_secondRow = $this->getSecond()->row();
        }

        // ustawianie danych
        $this->getSecondRow()->setData($this->_aDataSecond);
    }

    protected function saveSecond()
    {
        $sPrimaryOneKey = $this->getOne()->getPrimary();
        if ($iPrimaryOne = $this->getOneRow()->$sPrimaryOneKey) {
            $this->getSecondRow()->$sPrimaryOneKey = $iPrimaryOne;
        } else {
            throw new Exception("Brak id foreign for second relation OneToOne " . __CLASS__);
        }

        return $this->getSecondRow()->save();
    }

    public function join(\Zend\Db\Sql\AbstractPreparableSql $select)
    {
        $sOneTable = $this->getOne()->getTable();
        $sSecondTable = $this->getSecond()->getTable();
        $sPrimary = $this->getOne()->getPrimary();

        return $select->join($sSecondTable, $sOneTable . '.' . $sPrimary . '=' . $sSecondTable . '.' . $sPrimary, $this->_columnsSecond, $this->_joinType);
    }
}
