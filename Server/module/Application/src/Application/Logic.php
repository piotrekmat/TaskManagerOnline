<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */
/**
 * Description of Logic
 *
 * @author marcin
 */

namespace Application;

use \Application\Form\Dynamic\Database as Form;
use Application\Controller\Plugin\Params;
use \Zend\Mvc\Controller\Plugin\FlashMessenger;
use \Exception as Exception;

class Logic
{

    use \Application\Traits\Getter;

    /**
     *
     * @var type 
     */
    protected $_status = false;

    /**
     * 
     * @var Params 
     */
    protected $_oParams;

    /**
     * @var Form
     */
    protected $_oForm;

    /**
     * 
     * @var FlashMessenger 
     */
    protected $flashMessenger;

    public function __construct($oParams = null)
    {
        if (empty($oParams)) {
            
        } else {
            $this->setParams($oParams);
            $this->init();
        }
    }

    /**
     * 
     * @param Params $oParams   
     * @return self
     */
    public function setParams(Params $oParams)
    {
        $this->_oParams = $oParams;
        return $this;
    }

    public function init()
    {
        
    }

    /**
     * 
     * @return FlashMessenger
     */
    public function flashMessanger()
    {
        if (!$this->flashMessenger) {
            $this->flashMessenger = new FlashMessenger();
        }
        return $this->flashMessenger;
    }

    /**
     * 
     * @return Params
     */
    public function params()
    {
        return $this->get($this->_oParams);
    }

    /**
     * 
     * @return Form
     */
    public function form()
    {
        return$this->_oForm;
    }

    /**
     * Zwraca pojedyncze formularze (Fieldsety) w tablicy.
     * Wyłącznie dla relacji One To One i jej potomnych.
     * @return Form[]
     */
    public function splitForm()
    {
        return [$this->form()];
    }

    public function getStatus()
    {
        return $this->_status;
    }
}
