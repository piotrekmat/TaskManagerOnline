<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Link;

use \Exception as Exception;

class Button extends Link
{

    protected $_schema = '<form action="%s"><button %s>%s</button></form>';

    public function get()
    {
        return sprintf($this->_schema, $this->generateLink(), $this->generateAttributes(), $this->_name);
    }
}
