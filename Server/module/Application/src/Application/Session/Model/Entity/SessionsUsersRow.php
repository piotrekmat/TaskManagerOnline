<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Session\Model\Entity;

use \Application\Model\Entity\Row;

/**
 * Description of SessionsClients
 *
 * @author marcin
 */
class SessionsUsersRow extends Row
{

    protected $table = 'sessions_users';

    protected $_primary = [
        'id_user',
        'id_session'
    ];

}
