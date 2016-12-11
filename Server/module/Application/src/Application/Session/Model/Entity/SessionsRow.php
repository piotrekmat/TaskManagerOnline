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
 * Description of Session
 *
 * @author marcin
 */
class SessionsRow extends Row
{

    protected $table = 'session';

    protected $_primary = 'id_session';

}
