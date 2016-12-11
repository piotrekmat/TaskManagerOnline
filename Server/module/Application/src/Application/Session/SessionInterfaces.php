<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Session;

use \Exception as Exception;
/**
 *
 * @author marcin
 */
interface SessionInterfaces
{
    static function set($id);
    
    static function get();
}
