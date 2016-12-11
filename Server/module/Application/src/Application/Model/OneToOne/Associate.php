<?php


namespace Application\Model\OneToOne;

use \Application\Model\OneToOne as Model;
use \Application\Model\Entity\Table;
use \Application\Model\Entity\Row;
use \Zend\Db\Sql\Select;
use \Zend\Db\Sql\AbstractPreparableSql;
use \Exception as Exception;

/**
 * Description of Associate
 *
 * @author marcin
 */
class Associate extends Model
{

    protected $_associate;

    protected $_associateRow;

    protected $_aDataAssociate = [];

    protected $_columnsAssociate = ['*'];

    /**
     *
     * @return Table
     */
    public function getAssociate()
    {
        return $this->_associate = $this->get($this->_associate);
    }

    /**
     *
     * @return Row
     */
    public function getAssociateRow()
    {
        return $this->_associateRow;
    }

    protected function build()
    {
        parent::build();
        $this->buildAssociate();
        $this->saveAssociate();
    }

    protected function buildSecond()
    {
        if (!is_object($this->getSecondRow())) {

            $sOnePrimary = $this->getOne()->getPrimary();
            $sSecondPrimary = $this->getSecond()->getPrimary();
            $iOnePrimary = $this->getOneRow()->$sOnePrimary;

//            $select = new \Zend\Db\Sql\Select();

            $this->_secondRow = $this->getSecond()->row();
            if ($iOnePrimary) {
                $assocRow = $this->_associateRow = $this->getAssociate()->select([
                    $sOnePrimary => $iOnePrimary
                ])->current();
                if ($assocRow) {
                    $this->_secondRow = $this->getSecond()->select($assocRow->$sSecondPrimary);
                } else {
                    $this->_secondRow = $this->getSecond()->row();
                }
            }
        }
        $this->getSecondRow()->setData($this->_aDataSecond);
    }

    protected function buildAssociate()
    {
        $sPrimaryOne = $this->getOne()->getPrimary();
        $sPrimarySecond = $this->getSecond()->getPrimary();

        $iPrimaryOne = $this->getOneRow()->$sPrimaryOne;
        $iPrimarySecond = $this->getSecondRow()->$sPrimarySecond;

        $row = $this->getAssociate()->select([
            $sPrimaryOne => $iPrimaryOne,
            $sPrimarySecond => $iPrimarySecond
        ])->current();

        if (empty($row)) {
            $this->_associateRow = $this->getAssociate()->row();
            $this->getAssociateRow()->setData([
                $sPrimaryOne => $iPrimaryOne,
                $sPrimarySecond => $iPrimarySecond
            ]);
        } else {
            $this->_associateRow = $row;
        }
    }

    protected function saveAssociate()
    {
        try {
            return $this->getAssociateRow()->save();
        } catch (Exception $exc) {
            throw  $exc;
        }
    }

    /**
     *
     * @param AbstractPreparableSql $sql
     * @return AbstractPreparableSql
     */
    public function join(AbstractPreparableSql $sql)
    {
        $sOneTable = $this->getOne()->getTable();
        $sSecondTable = $this->getSecond()->getTable();
        $sAssociateTable = $this->getAssociate()->getTable();
        $sPrimaryOne = $this->getOne()->getPrimary();
        $sPrimarySecond = $this->getSecond()->getPrimary();


        $sql->join($sAssociateTable, $sOneTable . '.' . $sPrimaryOne . '=' . $sAssociateTable . '.' . $sPrimaryOne, $this->_columnsAssociate, $this->_joinType);
        $sql->join($sSecondTable, $sAssociateTable . '.' . $sPrimarySecond . '=' . $sSecondTable . '.' . $sPrimarySecond, $this->_columnsSecond, $this->_joinType);


//        echo $sql->getSqlString( new \Zend\Db\Adapter\Platform\Mysql());

        return $sql;
    }
}
