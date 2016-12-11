<?php

/**
 * Rozszerza dla modelu ORM One-To-Many o asocjację tabeli bazodanowej.
 * Assocjate zapewnie wyłącznie wprowadzenie danych do tablicy asocjacyjnej 
 * Gdzie dane wejściowe dla ->setData to dwie tablie kluczy głównych. 
 * Wpisy muszą występować po obu stronach Assocjacji.
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 */

namespace Application\Model;

use \Application\Model\Entity\Table;
use \Application\Model\Entity\Row;
use Zend\Db\Sql\Select;
use \Zend\Db\Sql\AbstractPreparableSql;
use \Exception as Exception;

class Associate extends OneToMany
{

    /**
     * Przechowuje obiekt Tabeli asocjacyjnej.
     * 
     * @var Table
     */
    protected $_associate;

    /**
     * 
     * @var Row[] 
     */
    protected $_associateRow;

    /**
     * $data[
     *  sKeyOnePrimary => id
     *  sKeyManyPrimary => [id,id,id]
     * ]
     * Przechowuje dane do wprowadzenia w Assocjacji
     * @var array 
     */
    protected $_aDataAssociate = [];

    /**
     * Zawiera columny dla selecta
     * @var array 
     */
    protected $_columnsAssociate = ['*'];

    /**
     * Flaga oznaczajaca usuwanie relacji Many
     * @var bool flaga x 
     */
    private $_bDeleteAssociate = false;

    /**
     * 
     * @param boolean $bool
     */
    public function _deleteAssociateWithError($bool)
    {
        $this->_bDeleteAssociate = $bool;
    }

    public function init()
    {
        
    }

    /**
     * Ustawia tabelę asocjacyjną
     * 
     * @param string|Table $associate
     */
    public function setAssociate($associate)
    {
        $this->_associate = $associate;
    }

    /**
     * 
     * @return Table
     */
    public function getAssociate()
    {
        return $this->_associate = $this->get($this->_associate);
    }

    /**
     * 
     * @return Row[]
     */
    public function getAssociateRow()
    {
        return $this->_associateRow;
    }

    protected function buildAssociate()
    {
        $manyPrimary = $this->getMany()->getPrimary();
        $onePrimary = $this->getOne()->getPrimary();
        $rowExist = [];
        $rowDelete = [];

        $dataOne = $this->getOneRow()->toArray()[$onePrimary];
        $this->_associateRow = $this->getAssociate()->select([
            $this->getOne()->getPrimary() => $dataOne
        ]);

        if (
            empty($this->_aDataAssociate) && !empty($this->getManyRow())
        ) {
            foreach ($this->getManyRow() as $oRowMany) {
                $this->_aDataAssociate[$manyPrimary][] = $oRowMany->$manyPrimary;
            }
        } elseif (empty($this->getManyRow()) && !empty($this->_aDataAssociate)) {
            if (!isset($this->_aDataAssociate[$onePrimary])) {
                throw new Exception("Brak danych assocjacji dla relacji One");
            }

            if (!isset($this->_aDataAssociate[$manyPrimary])) {
                throw new Exception("Brak danych assocjacji dla relacji Many");
            }
        } else {
            throw new Exception("Coś poszło nie tak, użyj setData lub setManyRow");
        }

        //aktualizacja => usuwanie i pozostawianie bez zmian.
        if (count($this->_associateRow)) {
            foreach ($this->_associateRow as $oRow) {
                if (in_array($oRow->$manyPrimary, $this->_aDataAssociate[$manyPrimary])) {
                    $rowExist[] = $oRow->$manyPrimary;
                } else {
                    $rowDelete[] = $oRow->$manyPrimary;
                    $oRow->delete();
                }
            }
        }

        $aElementsAssociate = array_diff($this->_aDataAssociate[$manyPrimary], $rowExist);

        $onePrimaryData = $this->getOneRow()->toArray()[$onePrimary];
        if (!$onePrimaryData) {
            throw new \Exception('Brak OneRow dla Assocjacji');
        }
        // Dodawanie nowych wierszy
        if (count($aElementsAssociate)) {
            foreach ($aElementsAssociate as $iElement) {
                $aId[] = $this->getAssociate()->insert([
                    $manyPrimary => $iElement,
                    $onePrimary => $onePrimaryData
                ]);
            }
            return $aId;
        }

        return true;
    }

    /**
     * 
     * @param array $aData
     * @return \Application\Model\Associate
     * @throws Exception
     */
    public function setData(array $aData = [])
    {
        $oneKeyPrimary = $this->getOne()->getPrimary();
        $manyKeyPrimary = $this->getMany()->getPrimary();

        // Przygotowanie One do Assocjacji
        if (is_object($this->getOneRow())) {
            if (empty($this->getOneRow()->$oneKeyPrimary))
                throw new \Exception('Jest obiekt nie ma primary ID ????? [' . __CLASS__ . ']');
        } elseif (array_key_exists($oneKeyPrimary, (array) $aData)) {
            $this->_oneRow = $this->getOne()->select(
                (int) $aData[$oneKeyPrimary]
            );

            if (!is_object($this->_oneRow)) {
                throw new \Exception('Brak Obiektu One [Row] dla _many w Assocjacji');
            }
        } else {
            throw new Exception('Brak klucza w tablicy [aData] metody ' . __METHOD__ . ' o nazwie klucza głównego [' . $this->getOne()->getPrimary() . ']tabeli bazy [' . $this->getOne()->getTable() . ']');
        }

        /**
         * @todo Czy aby tak przypadkiem powinno się to wrozyć, 
         *       Assocjacja wpada bez walidacji. - Bardoz źle.
         */
        if (isset($aData[$oneKeyPrimary])) {
            $this->_aDataAssociate[$oneKeyPrimary] = $aData[$oneKeyPrimary];
        }

        //przyspisuje istaniejącą lub pustą, jeśli nie istnieje. (potrzebne do odznaczenia danych);
        if (isset($aData[$manyKeyPrimary])) {
            $this->_aDataAssociate[$manyKeyPrimary] = $aData[$manyKeyPrimary];
        } else {
            $this->_aDataAssociate[$manyKeyPrimary] = [];
        }


        return $this;
    }

    /**
     * Zwraca usunięte elementy w assocjacji (Meny's ID)
     * @return boolean|array
     */
    protected function setDataDeleteFromAssosciate($aData)
    {
        if (empty($aData)) {
            $this->_aDataAssociate['delete'] = false;
        }
        $this->_aDataAssociate['delete'] = $aData;
    }

    /**
     * Zwraca usunięte elementy w assocjacji (Meny's ID)
     * @return boolean|array
     */
    protected function getDataDeleteFromAssosciate()
    {
        if (empty($this->_aDataAssociate['delete'])) {
            return false;
        }
        return $this->_aDataAssociate['delete'];
    }

    /**
     * 
     * $data[primaryMany] = array()
     * $data[praimryOne] = value
     * @return boolean
     * @throws \Exception
     */
    protected function build()
    {
        $this->buildAssociate();
    }

    /**
     * 
     * @return int[]
     */
    protected function saveAssociate()
    {
        $aId = [];
        $aAssociatePrimary = $this->getAssociate()->getPrimary();
        foreach ($this->getAssociateRow() as $oAssociateRow) {
            $oAssociateRow->save();
            foreach ($aAssociatePrimary as $sPrimary) {
                $data[$sPrimary] = $oAssociateRow->$sPrimary;
            }
            $aId[] = $data;
        }
        return $aId;
    }

    public function join(AbstractPreparableSql $sql)
    {
        $sOneTable = $this->getOne()->getTable();
        $sManyTable = $this->getMany()->getTable();
        $sAssociateTable = $this->getAssociate()->getTable();
        $sOnePrimary = $this->getOne()->getPrimary();
        $sManyPrimary = $this->getMany()->getPrimary();


        $sql->join(
            $sAssociateTable, $sOneTable . '.' . $sOnePrimary . '=' . $sAssociateTable . '.' . $sOnePrimary, $this->_columnsAssociate, 'left'
        );
        $sql->join(
            $sManyTable, $sManyTable . '.' . $sManyPrimary . '=' . $sAssociateTable . '.' . $sManyPrimary, $this->_columnsMany, 'left'
        );

        return $sql;
    }

    public function deleteMany($aData)
    {
        parent::deleteMany($aData);
    }
}
