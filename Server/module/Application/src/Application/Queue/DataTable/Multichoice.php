<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 * @todo Problem pojawia się, gdy usuwamy jedyną relację. Czyli chcemy One [1]  -> [0] (zaznaczonych elementów) Many.
 *  
 */

namespace Application\Queue\DataTable;

use \Application\Queue\DataTable;
use \Application\Link\Link;
use \Application\Model\Entity\Table;
use \Application\Form\Dynamic\Database as Form;
use \Application\Model\Model;
use \Zend\Mvc\Controller\Plugin\Params;
use \Zend\Mvc\Controller\Plugin\FlashMessenger;
use \Application\Queue\DataTable\Option\Checkbox;
use \Exception as Exception;

class Multichoice extends DataTable
{

    protected $_aPair;

    /**
     * Nazwę klucza, dla porównywania (klucz = nazwa kolumny dla setData)
     * 
     * @var type 
     */
    protected $_sKey;

    /**
     * Model dla tabeli asocjacyjnej bazy danych. \Application\Model\Model()
     * @var type 
     */
    protected $_oModel;

    protected $_oParams;

    protected $_flashMessenger;

    public function __construct(Model $oModel, Params $oParams = null)
    {
        $this->_oParams = $oParams;
        $this->setModel($oModel);
        if ($this->getModel()->getMany()) {
            $this->setKey($this->getModel()->getMany()->getPrimary());
        }

        parent::__construct($this->getModel()->getMany(), $this->where());
    }

    public function init()
    {
        // zapisywanie
        $this->save();
        $primaryKeyOne = $this->getModel()->getOne()->getPrimary();

        //przygotowanie do wyświetlenia
        $oModelData = $this->getModel()
            ->getAssociate()
            ->select([
                $primaryKeyOne => $this->params()->fromRoute($primaryKeyOne),
            ])
            ->toArray();

        $this->setAction(new Checkbox(), $oModelData);
        $this->setForm([
            'method' => 'POST'
        ]);

        return $this;
    }

    public function save()
    {
        $ids = null;
        if ($this->params()->fromPost()) {
            $aData = array_merge($this->params()->fromRoute(), $this->params()->fromPost());
            $this->getModel()->setData($aData);
            $ids = $this->getModel()->save();
        }
        return $ids;
    }

    /**
     * 
     * @param array $aPairs
     */
    public function setPair($aPairs)
    {
        foreach ($aPairs as $aPair) {
            $this->_aPair[] = $aPair[$this->_sKey];
        }
        //$this->_pair = $aPairs;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getPair()
    {
        return $this->_aPair;
    }

    /**
     * Ustawia nazwę klucza, dla porównywania (klucz = nazwa kolumny dla setData)
     * @param type $key
     */
    public function setKey($key)
    {
        $this->_sKey = $key;
    }

    /**
     * Zwraca nazwę klucza dla porównania (klucz = nazwa kolumny dla setData)
     * @return string
     */
    public function getKey()
    {
        return $this->_sKey;
    }

    public function setModel(Model $oModel)
    {
        $this->_model = $oModel;
        return $this;
    }

    /**
     * 
     * @return Model
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * POST/GET
     * @return Params;
     */
    public function params()
    {
        return $this->_oParams;
    }

    /**
     * @todo Przygotować pobieranie where z klasy Params;
     * @return type
     */
    public function where()
    {
        return $where = null;
    }

    public function getData()
    {
        $return = [];
        $i = 0;
        foreach ($this->_data as $columns) {
            foreach ($columns as $column => $value) {
                if (!in_array($column, $this->_hidden)) {
                    $return[$i][$column] = $this->checkData($column, $value);
                }
            }

            if (!empty($this->_action)) {
                /* @var $action Link */
                foreach ($this->_action as $action) {
                    if ($action instanceof Option\Checkbox) {
                        $action->setAttribute([
                            'name' => "$this->_sKey[]",
                            'value' => $columns[$this->_sKey],
                        ]);

                        if (in_array($columns[$this->_sKey], $this->_aPair)) {
                            $action->checked(true);
                        } else {
                            $action->checked(false);
                        }
                    }
                    $action->setData($columns);
                    $return[$i][$this->_actionName] .= (string) $action . ' ';
                }
            }
            $i++;
        }

        return $return;
    }

    /**
     * 
     * @param Link $action
     * @param array $pair // wartosć wymagana jeśli $action instance of Option\Checkbox
     */
    public function setAction($action, $pair = null)
    {
        if ($action instanceof Option\Checkbox) {
            if (!isset($pair)) {
                throw new Exception('Brak wymaganego 2 parametru [pair] w setAction przy Obiekt Option\Checkbox');
            }
            $this->setPair($pair);
        }
        return parent::setAction($action);
    }
}
