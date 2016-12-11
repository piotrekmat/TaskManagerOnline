<?php

/**
 * Kreator prostego formularza generowany na podstawie obiektu tabeli bazy danych,
 * Metoda form()  zwraca formularz, który można przekazać do widoku. 
 * Wymagana tplka layout AddEdit
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Logic;

use \Application\Form\Dynamic\Database as Form;
use \Application\Model\Entity\Table;
use \Application\Model\Entity\Row;
use \Zend\Mvc\Controller\Plugin\Params;
use \Zend\Mvc\Controller\Plugin\FlashMessenger;
use \Exception as Exception;

class Simple extends \Application\Logic
{

    /**
     * 
     * @var Table 
     */
    protected $_oModel;

    /**
     * 
     * @var Row 
     */
    protected $_oRow;

    /**
     * 
     * @param Form $form
     * @param Table $model
     * @param Params $params
     */
    public function __construct(Form $form, Table $model, Params $params)
    {
        $this->_oModel = $model;
        $this->_oForm = $form->generate();
        parent::__construct($params);
    }

    public function init()
    {
        if ($param = $this->params()->fromRoute($this->_oModel->getPrimary())) {
            $this->_oRow = $this->_oModel->select($this->params()->fromRoute($this->_oModel->getPrimary()));
        } else {
            $this->_oRow = $this->_oModel->row();
            $this->_oRow->setData($this->params()->fromRoute());
        }

        //$data = $this->_oRow->toArray(true);
        $data = $this->_oRow->toArray(true);
        $this->form()->setData($data);

        if ($this->params()->fromPost()) {
            $this->edit();
        }

        return $this->form();
    }

    protected function edit()
    {
        try {
            $this->form()->setData($this->params()->fromPost());
            if ($this->form()->isValid()) {
                $data = $this->form()->getData();
                $this->_oRow->setData($data);
                $this->_oRow->save();
                $this->flashMessanger()->addSuccessMessage($this->params()->fromRoute($this->_oModel->getPrimary()) ? 'Edytowano wpis poprawnie' : 'Dodano wpis do bazy');
                $this->form()->clearData();
            }
        } catch (\Exception $ex) {
            $this->flashMessanger()->addErrorMessage('Błąd dodania wpisu.' . $ex->getMessage());
        }
    }

    public function form()
    {
        return $this->_oForm;
    }
}
