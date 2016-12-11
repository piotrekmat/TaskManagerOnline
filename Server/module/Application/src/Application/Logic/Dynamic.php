<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Logic;

use Application\Model\Model;
use \Application\Model\OneToMany;
use Application\Model\Associate;
use Application\Model\ManyToMany;
use \Zend\Mvc\Controller\Plugin\Params;
use \Zend\Mvc\Controller\Plugin\FlashMessenger;
use \Zend\Form\Fieldset;
use \Application\Form\Dynamic\Database as Form;
use \Exception as Exception;

/**
 * @todo Poprawić wielokrotne generowanie formularza. (Generowanie w INIT i generowanie w Form)
 * OneToMany i ManyToMany
 */
class Dynamic extends \Application\Logic
{

    protected $_oModel;

    protected $_oParams;

    protected $_oDynamicForm;

    protected $_oStaticForm;

    protected $_oFlashMessenger;

    /**
     * Model: OneToOne, OneToMany
     * @param Params $oParams
     * @param Model $oModel
     */
    public function __construct(Params $oParams = null, Model $oModel = null)
    {
        if ($oModel)
            $this->setModel($oModel);

        parent::__construct($oParams);
    }

    public function init()
    {
        
    }

    public function setModel(Model $oModel)
    {
        $this->_oModel = $oModel;
    }

    /**
     * Model zawierający tabele i wiersze dla One i dla Many
     * @return ManyToMany
     */
    public function model()
    {
        return $this->_oModel = $this->get($this->_oModel);
    }

    /**
     * GET/POST
     * @return Params
     */
    public function params()
    {
        return $this->_oParams;
    }

    /**
     * 
     * @param \Application\Form\Form $oForm
     */
    public function setDynamicForm(Form $oForm)
    {
        $this->_oDynamicForm = $oForm;
    }

    /**
     * 
     * @return \Application\Form\Form;
     */
    public function getStaticForm()
    {
        return $this->_oStaticForm = $this->get($this->_oStaticForm);
    }

    /**
     * 
     * @param \Application\Form\Form $oForm
     */
    public function setStaticForm(Form $oForm)
    {
        $this->_oStaticForm = $oForm;
    }

    /**
     * 
     * @return \Application\Form\Form;
     */
    public function getDynamicForm()
    {
        return $this->_oDynamicForm = $this->get($this->_oDynamicForm);
    }

    private function splitForm()
    {
        return [];
    }
}
