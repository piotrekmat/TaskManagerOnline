<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Queue\DataTable\Option;

use \Application\Link\Link;
use \Exception as Exception;

class Checkbox extends Link
{

    protected $_schema = '<input type="checkbox" %s>%s</input>';

    public function __construct($name = '')
    {
        parent::__construct($name);
    }

    public function checked($bool = true)
    {
        if ($bool) {
            $this->_attributes['checked'] = '1';
        } else {
            unset($this->_attributes['checked']);
        }
    }

    public function get()
    {
        return sprintf($this->_schema, $this->generateAttributes(), $this->_name);
    }
}
