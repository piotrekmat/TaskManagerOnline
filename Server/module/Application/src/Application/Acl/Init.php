<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Acl;

use Application\Session;


/**
 * Description of Init
 *
 * @author marcin
 */
class Init
{

    use \Application\Traits\Singleton;

    public function __construct()
    {
        
    }

    public static function checkSession()
    {
        $session  = Session\Login::checkLoginId();
    }
}
