<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 * DataTable rezprezentuje wygląd danych w tabeli 
 * ( wyszukiwanie, paginowanie itp. oraz  odpowiednimi akcjami dla danych.)
 */

namespace Application\Queue;

use \Application\Queue\DataTable\Option;
use \Application\Link\Link;
use \Application\Model\Entity\Table;
use \Application\Model\Entity\Row;
use \Application\Form\Dynamic\Database as Form;
use \Exception as Exception;

class DataTable
{

    /**
     * Przechowuje nazwy kolumn.
     * @var type 
     */
    protected $_column = [];

    /**
     * Przechowuje dane zawartości.
     * @var array 
     */
    protected $_data = [];

    /**
     * Przechowuje nazwy kolumn ukrytych
     * @var array 
     */
    protected $_hidden = [];

    /**
     * Przechowuje nazwę kolumny Akcji
     * @var string 
     */
    protected $_actionName = '';

    /**
     * Przechowuje obiekt modelu tabeli bazy.
     * @var Table 
     */
    protected $_table;

    /**
     * Parametry dla form w <form>
     * @var array
     */
    protected $_form;

    /**
     * ['column_name' => Option\Dynamic ]
     * @var Option\Dynamic[] 
     */
    protected $_dynamic;

    /**
     * ['column_name' => Option\Dynamic ]
     * @var Option\Dynamic[] 
     */
    protected $_dictionary;

    /**
     * Przechowuje kolejność kolumn;
     */
    protected $_order = [];

    /**
     * Rodzaj sortowania [ASC | DESC] dla pierwszej kolumny
     */
    protected $_directionOrder = 'ASC';

    /**
     *
     * @var Option[] 
     */
    protected $_action = [];

    public function __construct(Table $oTable = null, $where = null)
    {
        if (is_object($oTable)) {
            $this->_table = $oTable;
            $this->setColumnName($this->_table->row()->getPrototype());
            $this->setData($this->_table->select($where)->toArray());
        }
        $this->init();
    }

    public function init()
    {
        
    }

    /**
     * Jakie kolumny danych mają zostać ukryte.
     * @param type $name
     * @return \Application\Queue\DataTable
     */
    public function setHidden($name)
    {
        if (is_array($name)) {
            $this->_hidden = array_merge($this->_hidden, $name);
        } elseif (is_string($name)) {
            if (!in_array($name, $this->_hidden)) {
                $this->_hidden[] = $name;
            }
        }
        return $this;
    }

    /**
     * Ustawia nazwę kolumny z przyciskami akcji.
     * @param type $name
     * @return \Application\Queue\DataTable
     */
    public function setColumnActionName($name)
    {
        $this->_actionName = $name;
        return $this;
    }

    /**
     * Ustawia nazwy kolumn
     * @param array|string $names
     * @return \Application\Queue\DataTable
     */
    public function setColumns($names)
    {
        if (is_array($names)) {
            foreach ($names as $name) {
                $this->setColumnName($name);
            }
        } else {
            $this->setColumnName($names);
        }
        return $this;
    }

    /**
     * Ustawia kolumny tabeli
     * @param array|string $name
     * @return \Application\Queue\DataTable
     */
    public function setColumnName($name)
    {
        if (is_array($name)) {
            $this->_column = array_merge($this->_column, $name);
        } elseif (is_string($name)) {
            if (!in_array($name, $this->_column)) {
                $this->_column[] = $name;
            }
        }
        return $this;
    }

    /**
     * Ustawia dane, na których podstawie generowane są linki dynamiczne.
     * @param array $aData
     * @return \Application\Queue\DataTable
     */
    public function setData($aData)
    {
        $this->_data = $aData;
        return $this;
    }

    /**
     * Zwraca tablice 0..x = [ key => wartosc] kolumn.
     * @return Array
     */
    public function getColumn()
    {

        $this->_column = $this->sortColumn($this->_column);

        foreach ($this->_column as $column) {
            if (!in_array($column, $this->_hidden)) {
                $return[] = $column;
            }
        }

        if (!empty($this->_action)) {
            $return[] = $this->_actionName;
        }

        return $return;
    }

    /**
     * Dodaje kolumnę danych.
     * @param string $name Określa nazwę kolumny
     * @param array $data Dane
     */
    public function addColumn($name, $data)
    {
        $this->setColumnName($name);
        $this->_data[$name] = $data;
    }

    /**
     * 
     * @param array $array [ nazwa_kolumny => array_opcji] 
     * np [type => [ opcja1 => 'nazwa1', opcja1 => 'nazwa2']]
     * OR [type => Object Entity / Table ]
     * @return \Application\Queue\DataTable
     */
    public function setDynamic(array $array)
    {
        foreach ($array as $key => $value) {
            $this->_dynamic[$key] = (new Option\Dynamic())->setColumn([$key => $value]);
        }
        return $this;
    }

    /**
     * 
     * @param array $array ['name_column' => Pole 'name' tabeli Dictionary] np. 'type' => 'typ_klienta'
     * @return \Application\Queue\DataTable
     */
    public function setDictionary(array $array)
    {
        foreach ($array as $key => $value) {
            $this->_dictionary[$key] = (new Option\Dynamic())->setDictionary($array);
        }
        return $this;
    }

    /**
     * Sprawdza dane i/lub realizuje na nich działania, jeśli wymaga tego kolumna
     * Zwraca wartość
     * @param string $column
     * @param string $value
     * @return string
     */
    public function checkData($column, $value)
    {
        switch (true) {
            case isset($this->_dictionary[$column]) :
                $oDynamic = $this->_dictionary[$column];
                $_value = $oDynamic->convert($value);
                break;

            case isset($this->_dynamic[$column]) :
                $oDynamic = $this->_dynamic[$column];
                $_value = $oDynamic->convert($value);
                break;
            default :
                $_value = $value;
                break;
        }

        return $_value;
    }

    /**
     * Zwraca dane tabeli w tablicy.
     * $return[$i][$column] = $value;
     * @return array
     */
    public function getData()
    {

        $return = [];
        $i = 0;
        foreach ($this->_data as $columns) {
            foreach ($columns as $column => $value) {
                if (!in_array($column, $this->_hidden)) {

                    /**
                     * @todo zamienić dane na obiekt, aby wykobywać na nim konkretne funkcje zamiany wartosci na konkretny schmemat, 
                     * np linków, butoonów, lub innych walidacji w czasie rzeczywistym.
                     */
                    switch (true) {
                        default:
                            $return[$i][$column] = $this->checkData($column, $value);
                            break;
                    }
                }
            }
            if (!empty($this->_action)) {
                /* @var $action Option */
                foreach ($this->_action as $action) {
                    $action->setData($columns);
                    $return[$i][$this->_actionName] .= (string) $action . ' ';
                }
            }

            $i++;
        }

        return $return;
    }

    /**
     * Ustawia przyciski akcji.
     * @param \Application\Link\Link[] $action
     * @return \Application\Queue\DataTable
     * @throws Exception
     */
    public function setAction($action)
    {
        //var_dump($action);
        if (!is_array($action)) {
            $action = [$action];
        }

        foreach ($action as $key => $option) {
            if ($option instanceof Link) {
                $this->_action[] = $option;
            } else {
                throw new Exception('Przycisk [' . $key . '] Akcji jest błędnej klasy');
            }
        }

        return $this;
    }

    /**
     * Ustawia parametry formularza wykorzystując i tworzy ZendForm checklist
     * @param array $options
     */
    public function setForm($options)
    {
        $this->_form = new \Application\Form\Form('dataTables', $options);
    }

    /**
     * 
     * @return type
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * Sprawdza czy DataTables jest formem
     * @return boolean
     */
    public function isForm()
    {
        if (is_object($this->_form)) {
            return true;
        }
        return false;
    }

    /**
     * Ustawia kolejności kolumn
     * Podawać nazwy kolumn, 
     * Wszystkie nazwy znajdujące się będą widoczne w podawanej kolejności (!hiddena)
     * Parametr TYPE decyduje o sortowaniu zawartości tabeli względem pierwszej ustawionej kolumny, czyli danych pierwszej kolumny. 
     * @param array $order 
     * @param string $type ['ASC' | 'DESC']
     */
    public function setOrderColumn(array $order, $type = null)
    {
        $this->_order = $order;
        if ($type) {
            $this->_directionOrder = $type;
        }
        return $this;
    }

    /**
     * Zwraca miejsce występowania kolumny [1..X] oraz sposób sortowania [ASC|DESC] 
     * @return bollean|array [1..x => ASC|DESC]
     */
    public function getOrderColumn()
    {
        return $this->_directionOrder;
    }

    /**
     * [name_column => ASC|DESC]
     * @param string $type
     */
    public function setDirectionOrder($type)
    {
        $this->_directionOrder = $type;
        return $this;
    }

    /**
     * Sortuje indeksy względem schematu $this->_order
     * @param array $array
     * @return array
     */
    public function sortColumn($array)
    {
        if (empty($this->_order)) {
            return $array;
        }

        return array_unique(array_merge($this->_order, $array));
    }
}
