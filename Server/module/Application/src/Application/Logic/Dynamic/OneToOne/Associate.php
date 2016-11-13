<?php

/**
 * 
 * @project: System partnerski SIFT
 * @author: Marcin Związek
 * 
 */

namespace Application\Logic\Dynamic\OneToOne;

use \Application\Logic\Dynamic\OneToOne;

/**
 * Description of Associate
 *
 * @author marcin
 */
class Associate extends OneToOne
{

    protected function setDataSecondForm()
    {
        $oDataSecondForm = $this->model()->getSecondRow();
        if (!is_object($oDataSecondForm)) {
            $sPrimaryOne = $this->model()->getOne()->getPrimary();
            $sPrimarySecond = $this->model()->getSecond()->getPrimary();
            $oAssociateData = $oDataSecondForm = null;

            $oAssociateData = $this->model()
                    ->getAssociate()
                    ->select([
                        $sPrimaryOne => $this->_iIdPrimaryOne
                    ])->current();


            if ($oAssociateData) {
                $oDataSecondForm = $this->model()
                    ->getSecond()
                    ->select($oAssociateData->toArray()[$sPrimarySecond]);
            }
        }

        if ($oDataSecondForm) {
            $this->getSecondForm()->setData($oDataSecondForm->toArray(true));
        }
    }
}
