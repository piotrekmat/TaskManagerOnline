<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Logic\Dynamic;

use Application\Logic\Dynamic;
use \Exception as Exception;

/**
 * Description of OneToMany
 *
 * @author marcin
 */
class OneToMany extends Dynamic
{

    public function getDynamicForm()
    {
        return $this->get($this->_oDynamicForm);
    }

    public function init()
    {
        /**
         * @todo Sprawdzenie uprawnień edycji / dodawania.
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
                 * To chyba jakiś stary mechanizm, moze jednak zrobić getData i setRow. 
                 * Do sprawdzenia
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

    public function form()
    {
        if (!$this->_oForm) {

            $sPrimaryOne = $this->model()->getOne()->getPrimary();
            $sPrimaryMany = $this->model()->getMany()->getPrimary();

            $idPrimary = $this->params()->fromRoute($sPrimaryOne);


            $oForm = $this->getStaticForm()
                ->generate();


            if ($idPrimary) {
                $aDataStaticForm = $this->model()
                    ->getOne()
                    ->select($idPrimary);


                $oForm->setData($aDataStaticForm->toArray(true));

                $aManyRow = $this->model()->getMany()->select([
                    $sPrimaryOne => $idPrimary
                ]);

                $i = 0;
                if (count($aManyRow)) {
                    foreach ($aManyRow as $aRow) {
                        $oDynamicForm = $this->getDynamicForm()
                            ->setRow($aRow)
                            ->setArrayName(true)
                            ->generate();

                        $oDynamicForm->remove('submit');
                        $fieldset = new \Zend\Form\Fieldset('Form' . $i++);
                        $fieldset->add($oDynamicForm);
                        $oForm->add($fieldset);
                    }
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
