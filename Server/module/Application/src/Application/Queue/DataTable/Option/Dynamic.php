<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Queue\DataTable\Option;

use \Application\Model\Entity\Row;
use \Application\Model\Entity\Table;
use \Exception as Exception;

/**
 * Description of Dynamic
 *
 * @author marcin
 */
class Dynamic
{

    use \Application\Traits\Getter;

    /**
     * Przechowuje Pola, które posiadają swoje wartości w słowniku.
     * ['nazwa_kolumny' => 'dictionary.name' | Object ]
     * @var array
     */
    protected $_dictionary = [];

    /**
     * Przechowuje Pola, które występują w innej tabeli
     * ['nazwa_kolumny' => 'Table.name' | Object ]
     * @var array 
     */
    protected $_column;

    /**
     *
     * @var Table 
     */
    protected $_table;

    /**
     *
     * @var array 
     */
    protected $_rows;

    /**
     *
     * @var array
     */
    protected $_keys = [];

    /**
     *
     * @var array 
     */
    protected $_data;

    /**
     * 
     */
    const empty_element = 'EMPTYDATA';

    /**
     * 
     * @param array $array
     * @return \Application\Queue\DataTable\Option\Dynamic
     * @throws Exception
     */
    public function setColumn(array $array)
    {
        foreach ($array as $key => $value) {

            switch (true) {
                case (is_string($value) || is_object($value)):
                    $this->setDynamic([$key => $value]);
                    break;

                case is_array($value) :
                    $this->setArray([$key => $value]);
                    break;
            }
        }

        return $this;
    }

    /**
     * Dictionary i Array wprowadzane są w taki sam sposób. 
     * _keys i _data mają te same indeksy podczas wprowadzania. 
     * @param array $array
     * @return \Application\Queue\DataTable\Option\Dynamic
     */
    protected function setArray(array $array)
    {
        $data = null;
        // petla wykona sei tylko raz
        foreach ($array as $key => $data) {
            $this->_column = $key;
            foreach ($data as $key => $value) {
                $this->_keys[] = $key;
                $this->_data[] = $value;
            }
        }

        return $this;
    }

    /**
     * Dynamic działa na podstawie wartości Tabeli i wyniku SetResults 
     * @param array $array
     * @return \Application\Queue\DataTable\Option\Dynamic
     * @throws Exception
     */
    protected function setDynamic(array $array)
    {
        foreach ($array as $key => $value) {
            $this->_column = $key;
            if (is_string($value))
                $value = $this->get($value); // init obiektu

            if ($value instanceof Table) {
                $this->_table = $value;
                $primary = $this->_table->getPrimary();
                $rows = $this->_table->select();
                /* @var $row Row */
                foreach ($rows as $row) {
                    $this->_rows[$row->$primary] = (string) $row;
                }
            } else {
                throw new Exception("Niewłaściwe dane!");
            }
        }
        return $this;
    }

    /**
     * Ustawia columnę dla wartosci pobieranych ze słownika.
     * @param array $array ['nazwa_kolumny' => dictionary.name]
     * @return DataTable
     */
    public function setDictionary(array $array)
    {
        $datas = null;
        // petla wykona się tylko raz (powinna)
        foreach ($array as $key => $value) {
            $this->_column = $key;
            if (is_string($value)) {
                $datas = \Preferences\Dictionary::getByName($value);
            } elseif (is_int($value)) {
                $datas = \Preferences\Dictionary::getById($value);
            }
            foreach ($datas as $data) {
                $this->_keys[] = $data['value'];
                $this->_data[] = $data['name'];
            }
        }

        return $this;
    }

    /**
     * 
     * @param string $column
     * @return boolean
     */
    public function check($column)
    {
        return ($this->_column === $column);
    }

    /**
     * 
     * @param int|string $value
     * @return boolean
     */
    protected function checkFromModel($value)
    {
        if (empty($value)) {
            $value = self::empty_element;
        }
        return isset($this->_rows[$value]);
    }

    /**
     * Pobiera wartość z tablicy wzgledem zadanej wartości
     * @param type $value
     * @return int|string
     */
    protected function getFromModel($value)
    {
        if (empty($value)) {
            $value = self::empty_element;
        }
        return $this->_rows[$value];
    }

    protected function checkFromDynamic($value)
    {
        if (empty($value)) {
            $value = self::empty_element;
        }

        if (false === array_search($value, $this->_keys))
            return false;

        return true;
    }

    protected function getFromDynamic($value)
    {
        if (empty($value)) {
            $value = self::empty_element;
        }

        $key = array_search($value, $this->_keys);
        return $this->_data[$key];
    }

    /**
     * Zwraca daną z tablicy względem wartosci podanej. 
     * @param strings $value
     * @return string
     */
    public function convert($value)
    {
        switch (true) {

            case $this->checkFromModel($value) :
                $_value = $this->getFromModel($value);
                break;

            case $this->checkFromDynamic($value) :
                $_value = $this->getFromDynamic($value);
                break;
        }

        return $_value ? $_value : $value;
    }
}
