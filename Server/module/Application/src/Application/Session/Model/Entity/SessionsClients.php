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
class SessionsClients extends Table
{

    protected $table = 'sessions_clients';

    protected $_primary = [
        'id_client',
        'id_session'
    ];

    protected $_row = '\Application\Model\Entity\SessionClientsRow';

}
