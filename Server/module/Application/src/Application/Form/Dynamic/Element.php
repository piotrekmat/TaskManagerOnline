<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Form\Dynamic;

use Application\Model\Entity\Table;
use \Exception as Exception;

/**
 * Description of Element
 *
 * @author marcin
 */
class Element
{

    protected $_element = [];

    protected $_params = [];

    protected $_empty = '';

    public function __construct()
    {
        
    }

    public function check($name)
    {
        if (isset($this->_element[$name])) {
            return true;
        }
        return false;
    }

    /**
     * Ustawia komunikat dla pustego pola.
     * @param type $string
     * @return \Application\Form\Dynamic\Element
     */
    public function setEmpty($string)
    {
        $this->_empty = $string;
        return $this;
    }

    /**
     * 
     * @param array $element ['name' => 'class_name' | Object | Array  ]
     * @param type $property ['name' => 'params' | Object | Array  ] Określają parametry potrzebne do utworzenia klasy
     * @return \Application\Form\Dynamic\Element
     */
    public function setElement(array $element, $property = null)
    {
        foreach ($element as $key => $value) {
            $this->_element[$key] = $value;
            if (isset($property[$key])) {
                $this->_params[$key] = $property[$key];
            }
        }

        return $this;
    }

    /**
     * Zwraca $return['options']['value_options'][$key];
     * @param string $name
     * @return null | array
     */
    public function getAtributes($name)
    {
        if ($this->check($name)) {
            $mTable = $this->_element[$name];
            if (is_string($this->_element[$name])) {
                $sClass = $this->_element[$name];
                $params = isset($this->_params[$name]) ? $this->_params[$name] : null;
                $mTable = new $sClass($params);
            }

            $data['options']['empty_option'] = $this->_empty;

            if ($mTable instanceof Table) {
                $primary = $mTable->getPrimary();
                $oRow = $mTable->select();

                if ($oRow->count()) {
                    //tworzenie schematu dla sortowania;
                    foreach ($oRow as $row) {
                        $aDoSort[] = [
                            'int' => $row->$primary,
                            'value' => (string) $row
                        ];
                    }

                    // Schemat dla sortowania
                    $sortArray = [];
                    foreach ($aDoSort as $sort) {
                        foreach ($sort as $key => $value) {
                            if (!isset($sortArray[$key])) {
                                $sortArray[$key] = [];
                            }
                            $sortArray[$key][] = $value;
                        }
                    }
                    array_multisort($sortArray['value'], SORT_ASC, $aDoSort);

                    //przypisywanie
                    foreach ($aDoSort as $datasort) {
                        $data['options']['value_options'][$datasort['int']] = $datasort['value'];
                    }
                }
            } elseif (is_array($mTable)) {
                foreach ($mTable as $key => $value) {
                    $data['options']['value_options'][$key] = $value;
                }
            } else {
                throw new Exception("Błędny obiekt. Generowanie dynamiczneog pola formularza in Form\Dynamic\Element");
            }

            return $data;
        }
        return null;
    }
}
