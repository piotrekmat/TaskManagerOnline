<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Logic\Dynamic;

use \Application\Logic\Dynamic;
use \Exception as Exception;

/**
 * Description of ManyToMany
 *
 * @author marcin
 */
class ManyToMany extends Dynamic
{

    protected $_dataMany = [];

    protected $_dataOne = [];

    protected $_data = [];

    public function init()
    {

        /**
         * @todo Sprawdzenie uprawnień edycji firmy.
         */
        if ($post = $this->params()->fromPost()) {

            $dataMany = $dataOne = [];
            $nameTableOne = $this->model()->getOne()->getTable();
            $nameTableMany = $this->model()->getMany()->getTable();

            //sortowanie danych
            foreach ($post as $key => $value) {
                if (is_array($value)) {
                    $dataMany[$key] = $value;
                } else {
                    $dataOne[$key] = $value;
                }
            }

            try {
                $formOne = $this->getStaticForm()->generate();
                $formOne->setData($dataOne);
                if ($formOne->isValid()) {
                    $this->_data[$nameTableOne] = $formOne->getData();
                }

                $size = 0;
                $i = 0;

                /**
                 * @todo Tu mi coś nie pasuje z tym Size ;/ do poprawki
                 */
                if (count($dataMany)) {
                    while ($i <= $size) {
                        $formMany = null;
                        $data = [];
                        foreach ($dataMany as $key => $value) {
                            $data[$key] = $value[$i];
                        }

                        $size = sizeof($value) - 1; // !!!!!
                        $formMany = $this->getDynamicForm()->generate();
                        $formMany->setData($data);
                        if ($formMany->isValid()) {
                            $this->_data[$nameTableMany][] = $formMany->getData();
                        }
                        $i++;
                    }
                }

                $this->model()->setData($this->_data)->save();
            } catch (\Exception $e) {
                $this->flashMessanger()->addErrorMessage($e->getMessage());
            }
        }
    }

    public function validateMany()
    {
        
    }

    public function validateOne()
    {
        
    }

    public function form()
    {
        if (!$this->_oForm) {

            $sPrimaryOne = $this->model()->getOne()->getPrimary();
            $sPrimaryMany = $this->model()->getMany()->getPrimary();

            $idPrimary = $this->params()->fromRoute($sPrimaryOne);


            $oForm = 
                $this->getStaticForm()
                ->generate();


            if ($idPrimary) {
                $aDataStaticForm = $this->model()
                    ->getOne()
                    ->select($this->params()->fromRoute($idPrimary))
                    ->current();

                $oForm->setData($aDataStaticForm->toArray(true));

                $select = $this
                    ->model()
                    ->getOne()
                    ->getSql() // pobieram sql select i łącze
                    ->select()
                    ->join(
                        $this->model()->getAssociate()->getTable(), $this->model()->getOne()->getTable() . '.' . $sPrimaryOne . '=' . $this->model()->getAssociate()->getTable() . '.' . $sPrimaryOne
                    )
                    ->join(
                        $this->model()->getMany()->getTable(), $this->model()->getMany()->getTable() . '.' . $sPrimaryMany . '=' . $this->model()->getAssociate()->getTable() . '.' . $sPrimaryMany
                    )
                    ->where($this->model()->getAssociate()->getTable() . '.' . $sPrimaryOne . '=' . $idPrimary);

                $aManyRow = $this->model()->getMany()->selectWith($select);


                $i = 0;
                foreach ($aManyRow as $row) {

                    $oDynamicForm = $this->getDynamicForm()
                        ->setRow($row)
                        ->setArrayName(true)
                        ->generate();

                    $oDynamicForm->remove('submit');
                    $fieldset = new \Zend\Form\Fieldset('Form' . $i++);
                    $fieldset->add($oDynamicForm);
                    $oForm->add($fieldset);
                }
            }

            $pustyForm = $this->getDynamicForm()
                ->setArrayName(true)
                ->generate();
            $pustyForm->setName('schema');
            $pustyForm->setAttribute("class", "dynamic");
            $pustyForm->setAttribute("id", "dynamic_{0}");
            $pustyForm->remove('submit');
            $fieldset = new \Zend\Form\Fieldset('schema');
            $fieldset->setLabel($this->model()->getMany()->getTable());
            $fieldset->add($pustyForm);

            $oForm->add($fieldset);

            $this->_oForm = $oForm;
        }

        return $this->_oForm;
    }
}
