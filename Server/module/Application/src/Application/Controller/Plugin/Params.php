<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller\Plugin;

use \Zend\Mvc\Controller\Plugin\Params as AbstractParams;

/**
 * Description of Params
 * Klasa ma funkcję zmiennych;
 * Normalnie bajpasy na szybko;
 * 
 * @author marcin
 */
class Params extends AbstractParams
{

    /**
     * Zmienna globalna
     * @var mix
     */
    protected $_data = [];

    /**
     * Tworzy zmienną globalną dostępna w metodach
     * @method fromPost | fromRoute
     * @param string $name
     * @param mix $value
     */
    public function setParam($name, $data = null)
    {
        $this->_data[$name] = $data;
    }

    public function fromPost($param = null, $default = null)
    {
        if (null === $param) {
            return array_merge(parent::fromPost($param, $default), $this->_data);
        }

        if (isset($this->_data[$param]))
            return $this->_data[$param];

        return parent::fromPost($param, $default);
    }

    public function fromRoute($param = null, $default = null)
    {
        if (null === $param) {
            return array_merge($this->_data, parent::fromRoute($param, $default));
        }

        if (isset($this->_data[$param]))
            return $this->_data[$param];

        return parent::fromRoute($param, $default);
    }
}
