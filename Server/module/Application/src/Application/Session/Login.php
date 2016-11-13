<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Session;

use Application\Session;
use Zend\Session\SessionManager;
use \Exception as Exception;

/**
 * Description of Session
 *
 * @todo Poprawić przenoszenie danych do/z bazy; session->database / database->session; aby odciążyć wielkość danych w session
 * @author marcin
 */
class Login extends Session
{

    use \Application\Traits\Getter;

    const _type = 'initialized';

    protected $data;

    public function __construct($key = null)
    {
        if (empty($key)) {
            $key = self::_type;
        }
        parent::__construct($key);
    }

    /**
     * Session Manager
     * @return SessionManager
     */
    public function getSessionMaganer()
    {
        return self::getDefaultManager();
    }

    /**
     * Ustawia Sesję dla ID użytkownika/clienta
     * Wywołanie wyłącznie z klasy User/Client
     * Ustawia Resources w sesji.
     * @param type $id
     */
    public static function set($id)
    {
        $container = new Session(self::_type);
        $container->login_type = self::_type;
        return $container;
    }

    /**
     * Zwraca ID klienta/usera
     * @return Session
     */
    public static function get()
    {
        return new self();
    }

    /**
     * Inicjalizacja sesji.
     * Jeśli użytkownik jest zalogowany, pobiera uprawienia.
     * @return Session
     */
    static public function checkLoginId()
    {
        $oInitSession = new self();
        switch ($oInitSession->login_type) {
            case Session\User::_type :
                return Session\User::get();
                break;

            case Session\Client::_type :
                return Session\Client::get();
                break;

            default :
                return false;
                break;
        }
    }

    static public function getResources()
    {
        $session = new Session('resources');
        if ($session) {
            return $session->default;
        }
        return false;
    }

    /**
     * Typ Sessji.
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Model OneToOne dla tabel
     * sessions
     * sessions_clients / sessions_users
     * @return \Application\Session\Model\Session;
     */
    public function getModel()
    {
        return $this->_oModel = $this->get($this->_oModel);
    }
}
