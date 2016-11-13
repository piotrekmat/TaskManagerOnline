<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Traits;

trait Getter
{

    /**
     * Podczas pobierania tworzy wymagany obiekt, lub zwraca istniejący.
     * 
     * @param string|object $var
     * @return 
     */
    protected function get($var)
    {
        if (empty($var)) {
            throw new \Exception("Can't create new Object from empty string.");
        }

        if (!is_object($var)) {
            return new $var();
        }
        return $var;
    }
}
