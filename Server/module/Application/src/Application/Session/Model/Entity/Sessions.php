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
 * Description of Session
 *
 * @author marcin
 */
class Sessions extends Table
{

    protected $table = 'session';

    protected $_primary = 'id_session';

    protected $_row = '\Application\Model\Entity\SessionsRow';

}
