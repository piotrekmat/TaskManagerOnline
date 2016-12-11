<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Model\Validate;

use \Zend\Validator\AbstractValidator;
use \Zend\Db\Metadata\Object\ColumnObject;

class Field extends AbstractValidator
{

    /**
     *
     * @var ColumnObject 
     */
    protected $field;

    /**
     * 
     * @param ColumnObject $oField
     */
    public function setField(ColumnObject $oField)
    {
        $this->field = $oField;
    }

    /**
     * 
     * @return ColumnObject 
     */
    public function getField()
    {
        return $this->field;
    }

    public function isValid($value)
    {

        return true;

        if ($this->getField()->getIsNullable()) {
            
        }

        switch ($this->getField()->getDataType()) {
            case '':
                break;
        }
    }
}
