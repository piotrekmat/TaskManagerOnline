<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Session\Model\Entity;

use \Application\Model\Entity\Table;

/**
 * Description of SessionsClients
 *
 * @author marcin
 */
class SessionsUsers extends Table
{

    protected $table = 'sessions_users';

    protected $_primary = [
        'id_user',
        'id_session'
    ];

    protected $_row = '\Application\Model\Entity\SessionUsersRow';

}
