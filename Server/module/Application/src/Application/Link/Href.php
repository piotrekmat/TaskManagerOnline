<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */
namespace Application\Link;
use \Exception as Exception;

class Href extends Link {
    
    protected $_schema = '<a %s href="%s">%s</a>';
}