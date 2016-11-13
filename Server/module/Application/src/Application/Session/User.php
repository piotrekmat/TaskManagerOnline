<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Session;

use Application\Session\Login as Session;
use \Exception as Exception;

/**
 * Description of User
 *
 * @author marcin
 */
class User extends Session implements SessionInterfaces
{

    const _type = 'user';

    protected $_oModel = '';

    /**
     * Ustawia Sesję dla ID użytkownika/clienta
     * Wywołanie wyłącznie z klasy User/Client
     * Ustawia Resources w sesji.
     * @param type $id
     */
    public static function set($id)
    {
        $container = new Session('initialized');
        $container->login_type = self::_type;
        $session = new self();
        $session->id = $id;
        return $session;
    }
    
    public static function logout()
    {
        $container = new self();
        $container->getManager()->getStorage()->clear(self::_type);
    }
}
