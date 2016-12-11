<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\View\Model;

use Zend\View\Model\ViewModel as AbstractViewModel;
use \Exception as Exception;

/**
 * Description of ViewModel
 *
 * @uses \Zend\View\Model\ViewModel;
 * @author marcin
 */
class ViewModel extends AbstractViewModel
{

    use \Application\Traits\Singleton;
}
