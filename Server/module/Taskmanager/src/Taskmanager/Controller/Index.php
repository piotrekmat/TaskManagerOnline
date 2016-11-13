<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Taskmanager\Controller;

use \Application\Controller\AbstractController;


class Index extends AbstractController
{

    public function indexAction()
    {
        try {

            return [
                "aaaa",
                "bbb",
                "cccc"
            ];
        } catch (Exception $ex) {
            throw $ex;
        }

        return $this->view();
    }
}
