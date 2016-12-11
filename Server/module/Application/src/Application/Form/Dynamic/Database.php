<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Form\Dynamic;

use \Application\Form\Dynamic;
use \Application\Model\Entity\Row as Row;
use \Zend\Db\Metadata\Object\ColumnObject;
use \Zend\Form\FormInterface;
use \Zend\InputFilter\InputFilter;
use \Exception as Exception;

/**
 * Description of Database
 *
 * @author marcin
 */
class Database extends Dynamic
{

    protected $_textarea = [
        'text',
        'longtext',
        'mediumtext'
    ];

    protected $_text = [
        'int'
    ];

    protected $_checkbox = [
        'tinyint',
        'bool',
        'boolean'
    ];

    /**
     * Przechowuje obiekt dziedziczący po \Application\Model\Entity\Row.
     * @var Row
     */
    protected $_row;

    /**
     * Przechowuje tablicę kolumn bazy danych, które w formularzą mają być ukryte.
     * @var Array 
     */
    protected $_hidden = [];

    /**
     * Przechowuje tablicę kolumn bazy danych, które nie mają występować w formularzu.
     * @var Array
     */
    protected $_disabled = [
        'create',
        'edit',
        'duch',
    ];

    /**
     * Przechowuje tablicę butonów wysyłania i anulowania.
     * @var type 
     */
    protected $_submit = [
        'zastosuj' => ''
    ];

    /**
     * Parametry elementu (Zend\Form\Elemenent) przy dynamicznym tworzeniu formularza.
     * @var array 
     */
    protected $_aParamElement = [];

    /**
     * Czy atrybut [name] Elementu Form ma być tablicowy.
     * @var boolean 
     */
    protected $_bArrayName = false;

    /**
     *
     * @var type 
     */
    protected $_dictionary = [];

    /**
     *
     * @var Dynamic\Element;
     */
    protected $_dynamic = '\Application\Form\Dynamic\Element';

    /**
     *
     * @var \Zend\InputFilter\Input[]
     */
    protected $_filters;

    /**
     * Flaga czy formularz został wygenerowany.
     * @var boolean
     */
    protected $_hasGenerateForm = false;

    /**
     *
     * @var \Zend\InputFilter\InputFilter[]
     */
    protected $_inputFilter;

    public function __construct($name = null, $options = array())
    {
        if ($this->_row) {
// wymagany w przypadku generowania formularza z bazy! 
// Ukrywanie kluczy głównych/obcych z bazy w formularzu.
            $this->setHiddenFields($this->row()->getPrimaryKey());
            $this->setHiddenFields($this->row()->getForeignKey());
        }

        if (!$name && $this->_row) {
            $name = $this->row()->getTable();
        }

        parent::__construct($name, $options);

        $this->init();
    }

    /**
     * Ustawia obiekt klasy Row, na którego podstawie generowany jest formularz.
     * @param Row $object
     * @return \Application\Form\Form
     */
    public function setRow(Row $object)
    {
        $this->_row = $object;
        return $this;
    }

    /**
     * Zwraca bieżący obiekt Row
     * @return Row
     * @throws Exception
     */
    public function row()
    {
        if (!is_object($this->_row)) {
            $this->_row = new $this->_row();
            if (!$this->row() instanceof Row) {
                throw new Exception('Nieprawidłowa instacja klasy _row');
            }
        }
        return $this->_row;
    }

    /**
     * Generuje formularz na podstawie column bazy danych [row()]
     * Brak danych oznacza pobranie ich z obiekt [Row]
     * Przy generowaniu data musi mieć format TableName_NameColumn
     * 
     * @param array $aData         
     * @throws Exception
     * @return Form
     */
    public function generate(array $aData = null)
    {
        // sprawdza i generuje obiekt Row  / !~exception
        $this->row();

        // ustawia flagę wygenerowania formularza z Row
        $this->_hasGenerateForm = true;

        //generowanie elementów
        $this->generateElementsFromDatabase();

        //ukrywanie pól
        $this->hiddenFields();

        //ustawia nazwę formularza 
        $this->setName($this->row()->getTable());

        //dodaje przycisku submit
        $this->addSubmit();

        // ustawia dane dla formularza
        $this->setData($aData);

        return $this;
    }

    /**
     * Inicjator obiektu
     * @return Dynamic\Element;
     */
    public function getDynamicElement()
    {
        if (!is_object($this->_dynamic)) {
            $dyn = $this->_dynamic;
            $this->_dynamic = new $dyn();
        }
        return $this->_dynamic;
    }

    /**
     * 
     * @param array ['name' => 'name_class' | Object | Array ] 
     * @param array $property  ['name' => 'name_class' | Object | Array ] Parametry do zbudowania klasy, jeśli nie można ich wskazać w przekazywaym obiekcie $element 
     */
    public function setDynamicElement(array $element, $property = null)
    {
        $this->getDynamicElement()->setElement($element, $property);
    }

    /**
     * Ustawia pamatr, czy nazwa elementu ma być tablicą dla potrzeb wysyłki.
     * @param boolean
     */
    public function setArrayName($boolean)
    {
        $this->_bArrayName = $boolean;
        return $this;
    }

    /**
     * Ustawia dodaje parametr [name] elemetu.
     * @param ColumnObject $oColumn
     * @return \Application\Form\Form
     */
    protected function setNameElement(ColumnObject $oColumn)
    {
        $name = $oColumn->getTableName() . '_' . $oColumn->getName();
        $this->_aParamElement['name'] = !$this->_bArrayName ? $name : $name . "[]";
        return $this;
    }

    /**
     * Ustawia wartość parametru [label] Elementu Form.
     * @param type $label
     * @return \Application\Form\Form
     */
    protected function setLabelElement($label)
    {
        /**
         * @todo Ustawić tłumaczenie Labela :-)
         */
        $this->_aParamElement['options']['label'] = ucfirst($label);
        return $this;
    }

    /**
     * Ustawia domyślną wartość elementu formularza 
     * @param ColumnObject $oColumn
     * @return \Application\Form\Form
     */
    protected function setDefaultValueElement(ColumnObject $oColumn)
    {
        if ($oColumn->getColumnDefault()) {
            
        }
        return $this;
    }

    /**
     * Ustawia wyamagalność elementu formularza.
     * @param ColumnObject $oColumn
     * @return \Application\Form\Form
     */
    protected function setRequiredElement(ColumnObject $oColumn)
    {
        if (!$oColumn->getIsNullable()) {
            $this->_aParamElement['attributes']['required'] = 'required';
        }
        return $this;
    }

    protected function selectFromDictionary($name)
    {
        $value_options = \Preferences\Dictionary::getByName($this->_dictionary[$name]);
        foreach ($value_options as $value) {
            $this->_aParamElement['options']['value_options'][$value['value']] = $value['name'];
        }
        return 'Zend\Form\Element\Select';
    }

    protected function selectFromEntity()
    {
        
    }

    /**
     * Ustawia typ elementu dla pola formularza.
     * @param ColumnObject $oColumn
     * @return self
     */
    protected function setTypeElement(ColumnObject $oColumn)
    {
        $type = $oColumn->getDataType();
        //var_dump($type);
        $name = $oColumn->getName();
        $this->_aParamElement['attributes']['class'] = 'form-group';
        switch (true) {

            case $this->checkDictionary($name) :
                $sType = $this->selectFromDictionary($name);
                break;

            case $this->getDynamicElement()->check($name) :
                $this->_aParamElement = $this->getDynamicElement()->getAtributes($name);
                $sType = '\Zend\Form\Element\Select';
                break;

            case in_array($type, $this->_textarea):
                $sType = '\Zend\Form\Element\Textarea';
                break;

            case in_array($type, $this->_text):
                $sType = '\Zend\Form\Element\Text';
                break;

            case in_array($type, $this->_checkbox):
                $sType = '\Zend\Form\Element\Checkbox';
                $this->_aParamElement['attributes']['class'] = 'checkbox';
                $this->_aParamElement['options'] = [
                    'checked_value' => '1',
                    'unchecked_value' => '0'
                ];
                break;

            default :
                $sType = '\Zend\Form\Element\Text';
                break;
        }


        $this->_aParamElement['type'] = $sType;
        return $this;
    }

    protected function setDefaultOption(ColumnObject $oColumn)
    {
        $mDefVal = $oColumn->getColumnDefault();
        if (null !== $mDefVal) {
            $this->_aParamElement['attributes']['value'] = $mDefVal;
        }
        return $this;
    }

    /**
     * Ustawia Form\Element na podstawie DB
     */
    protected function generateElementsFromDatabase()
    {
        $aColumns = $this->row()->getColumns();
        /* @var $oColumn ColumnObject */
        foreach ($aColumns as $oColumn) {
            if (!in_array($oColumn->getName(), $this->_disabled)) {
                $this->_aParamElement = []; // czyszczenie parametrów nowego elementu
                $this->setTypeElement($oColumn);
                $this->setRequiredElement($oColumn);
                $this->setDefaultValueElement($oColumn);
                $this->setNameElement($oColumn);
                $this->setLabelElement($oColumn->getName());
                $this->setDefaultOption($oColumn);
                $this->add($this->_aParamElement);
                $this->setFilter($oColumn);
            }
        }
    }

    /**
     * Dodaje przycski Wysyłania, jesli ustawiony parametr formularz przyjmuje przyciski OK, Anuluj, Zastosuj
     * Jeśli parametr == false ustawia wyłącznie przycisk "Zapisz";
     * @param boolean $advance
     * @return \Application\Form\Form
     */
    public function addSubmit($bAdvance = false)
    {
        return false;

        if ($bAdvance) {
            /**
             * @todo Ustawić przyciski /OK/ /Anuluj/ /Zastosuj/ i akcje do nich
             */
        }

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Zapisz',
                'id' => 'submitbutton',
            ],
        ]);

        return $this;
    }

    public function setData($data = null)
    {
//ustawianie danych
        if ($this->_hasGenerateForm) {
            if ($data) {
                $data = array_merge($this->row()->toArray(true), $data);
            } else {
                $data = $this->setDataFromRow();
            }
        }

        parent::setData($data);

        return $this;
    }

    protected function setDataFromRow()
    {

        if ($this->_bArrayName) {
// zmienia row->name => row->name[] w formularzu.
            $aData = [];
            $data = $this->row()->toArray(true);
            foreach ($data as $key => $value) {
                $aData[$key . '[]'] = $value;
            }
            $data = $aData;
        } else {
            $data = $this->row()->toArray(true);
        }

        return $data;
    }

    /**
     * Ustawia pola do ukrycia w formularzu na podsatwie zmiennej _hidden.
     * @param string|array $mField
     * @return \Application\Form\Form
     */
    public function setHiddenFields($mField = null)
    {
        if (is_array($mField)) {
            $this->_hidden = array_merge($this->_hidden, $mField);
        } elseif (is_string($mField)) {
            if (!in_array($mField, $this->_hidden)) {
                $this->_hidden[] = $mField;
            }
        }

        return $this;
    }

    /**
     * Ustawia do ukrycia i ukrywa pola w formularzu na podstawie zmiennej _hidden.
     * @param string|array $mField
     * @return \Application\Form\Form
     */
    protected function hiddenFields($mField = null)
    {
        $this->setHiddenFields($mField);
        $aPrototyp = [];
        if ($this->_hasGenerateForm) {
            $sTableName = $this->row()->getTable();
            $aPrototyp = $this->row()->getPrototype();
        }
        /**
         * @todo if exist in Database / if not exist  -> long name / short name;
         */
        foreach ($this->_hidden as $sField) {
            if (in_array($sField, $aPrototyp)) {
                $sName = $sTableName . '_' . $sField;
            } else {
                $sName = $sField;
            }

            if ($this->_bArrayName) {
                $sName = $sName . '[]';
            }

            $this->get($sName)->setAttributes([
                'type' => 'hidden',
                'required' => 0
            ]);
        }
        return $this;
    }

    /**
     * Ustawia pola do usunięcia w formularzu na podstawie zmiennej _disabled.
     * @param string|array $mField
     * @return \Application\Form\Form
     */
    public function setDisabledFields($mField = null)
    {
        if (is_array($mField)) {
            $this->_disabled = array_merge($this->_disabled, $mField);
        } elseif (is_string($mField)) {
            if (!in_array($mField, $this->_disabled)) {
                $this->_disabled[] = $mField;
            }
        }
        return $this;
    }

    /**
     * Ustawia do usunięcia i usuwa pola w formularza na podstawie zmiennej _disabled.
     * @param string|array $mField
     * @return \Application\Form\Form
     */
    protected function disabledFields($mField = null)
    {
        $this->setDisabledFields($mField);
        foreach ($this->_disabled as $field) {
            $this->remove($field);
        }
        return $this;
    }

    public function setShowFields($mFields)
    {
        if (!is_array($mFields)) {
            $mFields = [$mFields];
        }
        foreach ($mFields as $field) {
            if ($key = array_search($field, $this->_disabled)) {
                unset($this->_disabled[$key]);
            }
            if ($key = array_search($field, $this->_hidden)) {
                unset($this->_hidden[$key]);
            }
        }
        return $this;
    }

    public function clearData()
    {
        /**
         * @todo Coś tu trzeba wymyślić praktycznego.
         * $emptyData = array_fill_keys(array_keys((array) $aPostData), '');
         */
        unset($this->data);
    }

    /**
     * Ustawia wartość pola do pobrania ze słownika.
     * @param array $aDictionary
     */
    public function setDictionary(array $aDictionary)
    {
        $this->_dictionary = array_merge($aDictionary, $this->_dictionary);
        return $this;
    }

    /**
     * Sprawdza, czy pole ma ustawiony słownik
     * @param type $sName
     * @return boolean
     */
    public function checkDictionary($sName)
    {
        if (isset($this->_dictionary[$sName])) {
            return true;
        }
        return false;
    }

    /**
     * Walidacja
     */
    public function setFilter(ColumnObject $oColumn)
    {
        /**
         * @todo Poprawić filtrowanie dla pustych kluczy głownych. 
         * Wydajność !!!!! Bardzo słaba, przez to wszystko trwa 2-3sek.
         * if(!in_array($oColumn->getName(), $this->row()->getPrimaryKey() ))
         */
        $this->_filters[] = \Application\Form\Filter::getInstance()->getFilter($oColumn);
        return $this;
    }

    public function getInputFilter()
    {
        if (!$this->_inputFilter && $this->_filters) {
            $inputFilter = new InputFilter();
            foreach ($this->_filters as $oFilter) {
                $inputFilter->add($oFilter);
            }
            $this->_inputFilter = $inputFilter;
        }
//parent::getInputFilter();
        return $this->_inputFilter;
    }

    /**
     * Przy generowaniu formularzu z bazy podczas pobrania danych 
     * następuje powrót do krótkich nazw pól formularza. 
     * Transalacja z długich nazw formularza
     * 
     * @param type $flag
     * @return type
     */
    public function getData($flag = FormInterface::VALUES_NORMALIZED)
    {

        $data = parent::getData($flag);

        /**
         * @todo Czy tu jest coś narąbane, czy ja jestem nietrzeźwy jak to pisałem
         */
        // Powrót do krótki nazw, aby wykorzystać je do zapisanai w Modelu.
        if ($this->_hasGenerateForm) { // sprawdza czy było generowane
            $sTableName = $this->row()->getTable();
            $aPrototypeShort = $this->row()->getPrototype();
            foreach ($aPrototypeShort as $key) {
                $sLongName = $sTableName . '_' . $key;
                if (array_key_exists($sLongName, $data)) {
                    $_data[$key] = $data[$sLongName];
                }
            }
            $data = $_data;
        }

        return $data;
    }
}
