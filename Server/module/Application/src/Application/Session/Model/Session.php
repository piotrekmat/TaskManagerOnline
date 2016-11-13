<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Session\Model;

use \Application\Model\OneToOne as Model;
/**
 * Description of newPHPClass
 *
 * @author marcin
 */
class Session extends Model
{
    
    protected $_one = '\Application\Session\Model\Entity\Session';
    
    protected $_oneRow = '\Application\Session\Model\Entity\SessionRow';
    
}
