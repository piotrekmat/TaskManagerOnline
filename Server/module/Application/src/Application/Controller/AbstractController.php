<?php

/**
 *
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 *
 */

namespace Application\Controller;

use \Zend\Mvc\Controller\AbstractActionController;
use \Application\View\Model\ViewModel;

/**
 * @method \Application\Controller\Plugin\Params|mixed params(string $param = null, mixed $default = null)
 */
class AbstractController extends AbstractActionController
{

    /**
     * Przechowuje obiekt widowku akcji kontrolera.
     * @var ViewModel
     */
    private $view;

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {

    }

    public function setView($oView)
    {
        $this->view = $oView;
    }

    /**
     * Zwraca aktualny widok akcji kontrolera.
     * @return ViewModel
     */
    public function view()
    {
        if (null === $this->view) {
            $this->view = ViewModel::getInstance();
        }

        return $this->view;
    }

    /**
     * Zwraca Helper Managera przez service locatora.
     * @param string $helperName
     * @return type
     */
    protected function viewHelper($helperName)
    {
        return $this->getServiceLocator()->get('viewhelpermanager')->get($helperName);
    }

    protected function getViewHelper($helperName)
    {
        return $this->getServiceLocator()->get('viewhelpermanager')->get($helperName);
    }

    protected $em;

    /**
     * @var DoctrineORMEntityManager
     */
    public function getEntityManager()
    {
        if (null == $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }
}
