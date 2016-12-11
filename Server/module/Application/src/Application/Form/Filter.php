<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Form;

use \Zend\InputFilter\InputFilter;
use \Zend\InputFilter\Input;
use \Zend\Filter as ZendFilter;
use \Zend\Validator;
use \Zend\Db\Metadata\Object\ColumnObject;
use \Zend\Db\Metadata\Object\ConstraintObject;
use \Zend\Db\Metadata\Metadata;
use \Application\Model\Adapter as Adapter;
use \Exception as Exception;

/**
 * Description of Filter
 *
 * @author marcin
 */
class Filter
{

    protected static $_instance;

    protected $primary = [];

    protected $unique = [];

    protected $table;

    protected $constraints;

    /**
     * Singleton
     * @return Filter
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * 
     * @return \Zend\Db\Metadata\Metadata
     */
    public function getConstraints($table)
    {
        if ($this->table !== $table) {
            $this->table = $table;
            $this->constraints = (new Metadata(Adapter::getInstance(), $this->table))->getConstraints($this->table);

            /* @var $oConstraints  ConstraintObject */
            foreach ($this->constraints as $oConstraints) {
                switch (true) {
                    case $oConstraints->isPrimaryKey():
                        $this->primary[] = $oConstraints->getColumns();
                        break;
                    case $oConstraints->isUnique():
                        $this->unique[] = $oConstraints->getColumns();
                        break;
                }
            }
        }

        return $this->primary;
    }

    public function getFilter(ColumnObject $oColumn)
    {

        $this->getConstraints($oColumn->getTableName());

        $sName = $oColumn->getTableName() . '_' . $oColumn->getName();
        //$sName = $oColumn->getName(); /** Nazwy złożone */


        $oFilter = new Input($sName);



        $sType = $oColumn->getDataType();
        switch (true) {
            case in_array($sType, ['int']):
                $oFilter
                    ->getValidatorChain()
                    ->attach(new Validator\Digits());
                break;


            case in_array($sType, ['textarea']):
                /**
                 * @todo TextArea bez StripTags, a pozostałe tak... itp. // Do ustawienia. 
                 */
                break;

            default:
                $oFilter
                    ->getFilterChain()
                    ->filter(new ZendFilter\StripTags());

                break;
        }

        if ($oColumn->getCharacterMaximumLength()) {
            $oFilter
                ->getValidatorChain()
                ->attach(new Validator\StringLength(['min' => 0, 'max' => $oColumn->getCharacterMaximumLength()]));
        }


        if ($oColumn->getIsNullable() || in_array([$oColumn->getName()], $this->primary)) {
            $oFilter->setRequired(false);
            $oFilter->allowEmpty(true);
        } else {
            $oFilter->setRequired(true);
            $oFilter->allowEmpty(false);
        }


        /**
         * 
         * @todo Błąd "Exist record in DB" podczas aktualiazcji danych.
        
          if (in_array([$oColumn->getName()], $this->unique)) {
          $oFilter
          ->getValidatorChain()
          ->attach(new Validator\Db\NoRecordExists([
          'table' => $oColumn->getTableName(),
          'field' => $oColumn->getName(),
          'adapter' => Adapter::getInstance(),
          'messages' => [\Zend\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND => 'Nazwa już istnieje.']
          ])
          );
          }
         */
        //$oFilter->breakOnFailure();


        return $oFilter;
    }
}
