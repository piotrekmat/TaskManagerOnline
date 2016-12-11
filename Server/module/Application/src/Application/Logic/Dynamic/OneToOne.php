<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Logic\Dynamic;

use \Application\Logic;
use \Application\Model\OneToOne as Model;
use \Application\Form\Dynamic\Database as Form;
use \Zend\Db\Sql\Select;
use \Exception as Exception;

/**
 * Description of OneToOne
 *
 * @author marcin
 */
class OneToOne extends Logic
{

    use \Application\Traits\Getter;

    protected $_oModel;

    protected $_oOneForm;

    protected $_oSecondForm;

    protected $_aDataOne;

    protected $_aDataSecond;

    protected $_iIdPrimaryOne;

    /**
     * 
     * @return Model
     */
    public function model()
    {
        return $this->_oModel = $this->get($this->_oModel);
    }

    /**
     * 
     * @return Form
     */
    public function getOneForm()
    {
        return $this->_oOneForm = $this->get($this->_oOneForm);
    }

    /**
     * 
     * @return Form
     */
    public function getSecondForm()
    {
        return $this->_oSecondForm = $this->get($this->_oSecondForm);
    }

    protected function setDataOne($aData)
    {
        $this->getOneForm()->generate()->setData($aData);
        if ($this->getOneForm()->isValid()) {
            $this->_aDataOne = $this->getOneForm()->getData();
        }else{
            var_dump($this->getOneForm()->getMessages());
        }
    }

    protected function setDataSecond($aData)
    {
        $this->getSecondForm()->generate()->setData($aData);
        if ($this->getSecondForm()->isValid()) {
            $this->_aDataSecond = $this->getSecondForm()->getData();
        }else{
            var_dump($this->getSecondForm()->getMessages());
        }
    }

    public function init()
    {
        try {
            /**
             * @todo Tutaj sprawdzanie po Id primary z GET / Hmmm
             * Brak ID z GET podczas wprowadzania danych.../ Id Primary z relacji One
             */
            if ($post = $this->params()->fromPost()) {
                $get = $this->params()->fromQuery();
                $data = array_merge($post, $get);
                $this->setDataOne($data);
                $this->setDataSecond($data);

                $nameTableOne = $this->model()->getOne()->getTable();
                $nameTableSecond = $this->model()->getSecond()->getTable();

                $aData = [
                    $nameTableOne => $this->_aDataOne,
                    $nameTableSecond => $this->_aDataSecond
                ];

                $this->model()->setData($aData)->save();
                $this->flashMessanger()->addSuccessMessage('Zmiany wprowadzone poprawnie');
            }
        } catch (Exception $e) {
            if ($e instanceof \Zend\Db\Adapter\Exception\InvalidQueryException) {
                $this->flashMessanger()->addErrorMessage("BŁĄD BAZY DANYCH" . $e->getMessage());
            } else {
                $this->flashMessanger()->addErrorMessage("BŁĄD LOGICZNY" . $e->getMessage());
            }
            throw $e;
        }
    }

    public function form()
    {
        if (!$this->_oForm) {
            $sPrimaryOne = $this->model()->getOne()->getPrimary();
            $this->_iIdPrimaryOne = $this->params()->fromRoute($sPrimaryOne);

            $this->getOneForm()->generate()->remove('submit');

            $this->getSecondForm()->generate();

            if ($this->_iIdPrimaryOne) {
                $this->setDataOneForm();
                $this->setDataSecondForm();
            }

            $oElementCsrf = new \Zend\Form\Element\Csrf('test');
            $oButtonSubmit = new \Zend\Form\Element\Submit('send');
            $oButtonSubmit->setValue('Save');


            $oForm = new \Zend\Form\Form('form');
            $oForm->add($this->getOneForm());
            $oForm->add($this->getSecondForm());
            $oForm->add($oElementCsrf);
            $oForm->add($oButtonSubmit);

            $this->_oForm = $oForm;
        }

        return $this->_oForm;
    }

    /**
     * Zwraca pojedyncze formularze w tablicy
     * Form[]
     */
    public function splitForm()
    {
        $this->form();
        return [
            $this->getOneForm(),
            $this->getSecondForm(),
        ];
    }

    protected function setDataOneForm()
    {
        $oDataOneForm = $this->model()->getOneRow();
        if (!is_object($oDataOneForm)) {
            $oDataOneForm = $this->model()
                ->getOne()
                ->select($this->_iIdPrimaryOne);
        }
        if ($oDataOneForm) {
            $this->getOneForm()->setData($oDataOneForm->toArray(true));
        }

        return $this->getOneForm();
    }

    protected function setDataSecondForm()
    {

        $oDataSecondForm = $this->model()->getSecondRow();
        if (!is_object($oDataSecondForm)) {
            $sPrimaryOne = $this->model()->getOne()->getPrimary();
            $oDataSecondForm = $this->model()
                    ->getSecond()
                    ->select([
                        $sPrimaryOne => $this->_iIdPrimaryOne
                    ])->current();
        }

        if ($oDataSecondForm) {
            $this->getSecondForm()->setData($oDataSecondForm->toArray(true));
        }
    }
}

/**
function (Select $select) {
            $sKeyPrimaryOne = $this->model()->getOne()->getPrimary();
            $sName = $this->model()->getSecond()->getPrimary();
            $sOrder = "$sName DESC";
            $select->where([
                $sKeyPrimaryOne => $this->_iIdPrimaryOne
            ]);
            $select->order($sOrder)->limit(1);
        }
 
 */