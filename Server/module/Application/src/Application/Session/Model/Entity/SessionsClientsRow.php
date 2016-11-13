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
class SessionsClientsRow extends Row
{

    protected $table = 'sessions_clients';

    protected $_primary = [
        'id_client',
        'id_session'
    ];

}
