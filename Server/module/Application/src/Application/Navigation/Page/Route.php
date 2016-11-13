<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Navigation\Page;

/**
 * Description of Route
 *
 * @author marcin
 */
class Route extends \Zend\Navigation\Page\AbstractPage
{

    public function getHref()
    {
        $string = '/' . $this->module .
            '/' . $this->controller .
            '/' . $this->action;

        if (!empty($this->params)) {
            $output = implode('/', array_map(function ($v, $k) {
                    return sprintf("%s/%s", $k, $v);
                }, $this->params, array_keys($this->params)));
            $string .= '/' . $output;
        }

        return $string;
    }
}
