<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application;

use Zend\Session\Container;
use Zend\Session\SessionManager;
use \Exception as Exception;

/**
 * Description of Session
 *
 * @author marcin
 */
class Session extends Container
{

    static public function logout()
    {
        Session\User::logout();
        Session\Client::logout();
    }
}
