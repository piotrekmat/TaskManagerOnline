<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Link;

use \Exception as Exception;

class Javascript extends Link
{

    const EMPTY_DYNAMIC = 'this';

    protected $_schema = '<button %s onclick=\'%s\'>%s</button>';

    public function __construct($name, $functions = array())
    {
        $this->setName($name);
        $this->setFunctions($functions);
    }

    public function setFunctions($functions)
    {
        if (!is_array($functions)) {
            $functions = [$functions => null];
        }
        foreach ($functions as $function => $params) {
            $this->_static = $function;
            if (!is_array($params)) {
                $params = [$params];
            }
            $this->_dynamic = $params;
        }
        return $this;
    }

    public function get()
    {
        return sprintf($this->_schema, $this->getClass(), $this->generateLink(), $this->_name);
    }

    public function generateLink()
    {
        return sprintf("%s(%s)", $this->generateFunctions(), $this->generateParams());
    }

    public function generateFunctions()
    {
        return (string) $this->_static;
    }

    public function generateParams()
    {
        $explode = [];
        foreach ($this->_dynamic as $param) {
            if (isset($this->_data[$param])) {
                if (is_numeric($this->_data[$param])) {
                    $explode[] = (int) $this->_data[$param];
                } else {
                    $explode[] = (string) '"' . $this->_data[$param] . '"';
                }
            } else {
                $explode[] = self::EMPTY_DYNAMIC;
            }
        }
        return (string) implode(',', $explode);
    }

    public function isAllowed()
    {
        return true;
    }
}
