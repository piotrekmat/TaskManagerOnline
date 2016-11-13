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
 * Description of Client
 *
 * @author marcin
 */
class Client extends Session implements SessionInterfaces
{

    const _type = 'client';

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
        $session = new self(self::_type);
        $session->id = $id;
        return $session;
    }

    public static function logout()
    {
        $container = new self(self::_type);
        $container->getManager()->getStorage()->clear(self::_type);
        var_dump($_SESSION);
        unset($_SESSION);
    }
}
