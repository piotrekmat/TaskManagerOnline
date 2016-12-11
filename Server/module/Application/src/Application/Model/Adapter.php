<?php

/**
 * Adapter bazy danych: use ::getInstance();
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 *
 */

namespace Application\Model;

use \Application\Services\ServiceLocatorFactory;
use \Exception as Exception;

class Adapter extends \Zend\Db\Adapter\Adapter
{

    /**
     *
     * @var Adapter
     */
    private static $_oInstance;

    /**
     * @return Adapter
     */
    static function getInstance()
    {
        if (null === self::$_oInstance) {

            $sm = ServiceLocatorFactory::getInstance();
            self::$_oInstance = $sm->get('Zend\Db\Adapter\Adapter');
            self::$_oInstance->query("SET NAMES 'utf8' COLLATE 'utf8_unicode_ci'")->execute();
        }
        return self::$_oInstance;
    }
}
