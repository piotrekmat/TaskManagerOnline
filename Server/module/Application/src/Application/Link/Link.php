<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Link;

use \Exception as Exception;

abstract class Link
{

    protected $_class = '';

    protected $_schema;

    protected $_target = '_self';

    protected $_attributes;

    protected $_name;

    protected $link;

    protected $_static;

    protected $_dynamic;

    protected $_data;

    protected $_access;

    protected $_map = [
        'module',
        'controller',
        'action',
    ];

    public function __construct($name, $static = [], $dynamic = [])
    {
        $this->setName($name);
        $this->setStatic($static);
        $this->setDynamic($dynamic);
    }

    /**
     * Nazwa linku.
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * Ustawia statyczne dane w linku.
     * @param array $static
     * @return \Application\Link\Link
     * @throws Exception
     */
    public function setStatic($static)
    {
        if (!empty($static)) {
            if (is_array($static)) {
                foreach ($static as $key => $value) {
                    if (in_array($key, $this->_map)) {
                        if ($key === 'module') {
                            $value = $this->getModuleRoute($value);
                        }
                        $this->_access[$key] = $value;
                    } else {
                        $this->setDynamic([$key => $value]);
                    }
                }
            } else {
                throw new Exception("Błąd linku statycznym [static]. " . __CLASS__ . "[$static");
            }
        }
        return $this;
    }

    /**
     * 
     * @param type $dynamic
     * @throws Exception
     */
    public function setDynamic($dynamic)
    {
        if (!empty($dynamic)) {
            if (is_array($dynamic)) {
                $this->_dynamic = $dynamic;
            } else {
                throw new Exception("Błąd w danych dynamicznych [dynamic]" . __CLASS__ . "[$dynamic]");
            }
        }
    }

    /**
     * Ustawia nazwy klas w linku.
     * @param array|string $names
     * @return \Application\Link\Link
     */
    public function setClass($names)
    {
        if (!is_array($names)) {
            $names = [$names];
        }
        $this->_class = array_merge($this->_class, $names);
        return $this;
    }

    /**
     * Zwraca string class="nazwa_klaasy1 nazwa_klasy2" 
     * @return string
     */
    public function getClass()
    {
        $unpack = '';
        $schema = 'class="%s"';
        if (!empty($this->_class))
            foreach ($this->_class as $class) {
                $unpack .= " " . $class;
            }
        return sprintf($schema, $unpack);
    }

    /**
     * Ustawia target Link.
     * @param string $name
     * @return \Application\Link\Link
     */
    public function setTarget($name)
    {
        $allow = [
            '_blank',
            '_self',
            '_parrent',
            '_top'
        ];
        if (in_array($name, $allow)) {
            $this->_target = $name;
        }
        return $this;
    }

    /**
     * Zwraca strin atrybutu target domyślnie: [target="_self"]
     * @return string
     */
    public function getTarget()
    {
        $target = '';
        if ($this->_target) {
            $schema = 'target="%s"';
            $target = sprintf($schema, $this->_target);
        }

        return $target;
    }

    /**
     * Ustawia atrybut linku.
     * @param array $options
     * @return Link
     */
    public function setAttribute($options = [])
    {
        if (!empty($options) && is_array($options)) {
            foreach ($options as $attr => $val) {
                $this->_attributes[$attr] = $val;
            }
        }
        return $this;
    }

    /**
     * Zwraca wartość istniejącego atrybutu
     * @param string $attr
     * @return mix
     */
    public function getAttribute($attr)
    {
        return $this->_attributes[$attr];
    }

    public function getModuleRoute($value)
    {

        /**
         * @todo To jakas masakra, aby wyciągnąć default routing modułu;
         */
        $sDefaultRout = \Application\Services\ServiceLocatorFactory::getInstance()->get('Config')['router']['routes'][strtolower($value)]['options']['route'];
        return substr($sDefaultRout, 1);
    }

    /**
     * Tworzy dynamiczny link: /parametr/wartosc -> wartosc z setData($wartosci)
     * @return string
     */
    public function generateDynamicAddress()
    {
        $dynamic = '';
        if (is_array($this->_dynamic)) {
            foreach ($this->_dynamic as $param => $value) {
                if ($this->_data[$param]) {
                    $dynamic .= '/' . $param . '/' . $this->_data[$param];
                } elseif ($this->_data[$value]) {
                    $dynamic .= '/' . $value . '/' . $this->_data[$value];
                } else {
                    $dynamic .= '/' . $param . '/' . $value;
                }
            }
        }
        return $dynamic;
    }

    /**
     * Zwraca string adresu /Module/Controller/Action/parametr/wartosc_statyczna
     * @return string
     */
    public function generateStaticAddress()
    {
        $staticLink = '';
        if (is_array($this->_access)) {
            foreach ($this->_access as $param) {
                $staticLink .= '/' . $param;
            }
        }

        if (is_array($this->_static)) {
            foreach ($this->_static as $key => $value) {
                $staticLink .= '/' . $key . '/' . $value;
            }
        }

        return $staticLink;
    }

    /**
     * Zwraca goły link. /module/controller/action/parametr/wartosc ... /
     * @return string
     */
    public function generateLink()
    {
        return strtolower($this->generateStaticAddress() . $this->generateDynamicAddress());
    }

    /**
     * Ustawia dane dla parametrów dynamicznych.
     * @param string|int $data
     */
    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * Generuje atrubuty takie jak class="" target="" id="" linku
     * @return string
     */
    public function generateAttributes()
    {
        $attributes = '';
        if (!empty($this->_attributes)) {
            foreach ($this->_attributes as $attr => $value) {
                $attributes .= sprintf('%s="%s" ', $attr, $value);
            }
        }
        return $this->getClass() . ' ' . $this->getTarget() . ' ' . $attributes;
    }

    /**
     * Zwraca cały link ze znacznikami html i atrybutami.
     * @return type
     */
    public function get()
    {

        return (string) sprintf($this->_schema, $this->generateAttributes(), $this->generateLink(), $this->_name);
    }

    /**
     * Zwraca cały link ze znacznikami HTML atrybutami, jeśli użytkownik ma dostęp do przycisku, wyswietla go. 
     * @return type
     */
    public function __toString()
    {
        if ($this->isAllowed()) {
            return "{$this->get()}";
        }
    }

    /**
     * Sprawdza uprawnienia do /Module/Controller/Action  na podstawie \Application\Acl
     * @todo Dodać sprawdzanie uprawnień do przycisku;
     * @return boolean
     */
    public function isAllowed()
    {
        return \Auth\Acl::getInstance()->isAllowed($this->_access['module'], $this->_access['controller'], $this->_access['action']);
    }
}
