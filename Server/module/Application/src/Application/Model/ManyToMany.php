<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Model;

use \Zend\Db\Sql\AbstractPreparableSql;
use \Exception as Exception;

/**
 * Klasa tworzy osobno obiekty w tabeli One i tabeli Many, 
 * a zaraz po ich stworzeniu łącze je w tablicy assocjacyjnej
 */
class ManyToMany extends Associate
{

    private $_aDataMany = [];

    protected $_bDeleteMany = true;

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
        $sOnePrimaryKey = $this->getOne()->getPrimary();
        $sManyPrimaryKey = $this->getMany()->getPrimary();

        $this->buildOne();
        $this->buildMany();

        $this->_aDataAssociate[$sOnePrimaryKey] = $this->saveOne();
        $this->_aDataAssociate[$sManyPrimaryKey] = $this->saveMany();

        $this->buildAssociate();
    }

    protected function buildMany()
    {
        if (empty($this->_aDataMany)) {
            throw new \Exception("Brak danych do zapisu");
        }
        $oneKeyPrimary = $this->getOne()->getPrimary();
        $manyKeyPrimary = $this->getMany()->getPrimary();

        $this->_associateRow = $this->getAssociate()->select([$oneKeyPrimary => $this->getOneRow()->$oneKeyPrimary]);

        foreach ($this->getAssociateRow()->toArray() as $associateRow) {
            //tworze tablice zawierającą tylko asocjację z kluczami głównymi Many, dla porównania, czy ID występują.
            $aAssociateRow[] = $associateRow[$manyKeyPrimary];
        }

        foreach ($this->_aDataMany as $dataMany) {
            if ($dataMany[$manyKeyPrimary] && in_array($dataMany[$manyKeyPrimary], $aAssociateRow)) {
                $manyRow = $this->getMany()->select($dataMany[$manyKeyPrimary]);
            } elseif ($dataMany[$manyKeyPrimary]) {
                // jesli w danych wprowadzonych występuje ID, ale nie występuje w Assocjacji // jakis wałek.
                continue;
            } else {
                $manyRow = $this->getMany()->row();
            }

            $manyRow->setData($dataMany);
            $this->_manyRow[] = $manyRow;
        }
    }

    protected function saveMany()
    {
        $aId = [];
        $sManyPrimary = $this->getMany()->getPrimary();
        foreach ($this->getManyRow() as $oManyRow) {
            $oManyRow->save();
            $aId[] = $oManyRow->$sManyPrimary;
        }
        return $aId;
    }

    protected function saveOne()
    {
        $aId = [];
        $sOnePrimary = $this->getOne()->getPrimary();
        foreach ($this->getOneRow() as $oOneRow) {
            $oOneRow->save();
            $aId[] = $oOneRow->$sOnePrimary;
        }
        return $aId;
    }
}
