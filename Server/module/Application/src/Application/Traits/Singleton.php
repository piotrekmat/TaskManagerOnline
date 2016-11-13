<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */
/**
 * Description of Singleton
 *
 * @package \Application\Traits
 * @author marcin
 */

namespace Application\Traits;

trait Singleton
{

    protected static $_instance;

    /**
     * Pobranie instancji obiektu.
     * 
     * @static
     * @return this
     */
    public static function getInstance()
    {

        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}
