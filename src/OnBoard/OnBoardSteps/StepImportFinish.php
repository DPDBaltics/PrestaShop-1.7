<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 *  International Registered Trademark & Property of INVERTUS, UAB
 */

namespace Invertus\dpdBaltics\OnBoard\OnBoardSteps;

use DPDBaltics;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\OnBoard\AbstractOnBoardStep;
use Invertus\dpdBaltics\OnBoard\Objects\OnBoardButton;
use Invertus\dpdBaltics\OnBoard\Objects\OnBoardFastMoveButton;
use Invertus\dpdBaltics\OnBoard\Objects\OnBoardParagraph;
use Invertus\dpdBaltics\OnBoard\Objects\OnBoardTemplateData;

class StepImportFinish extends AbstractOnBoardStep
{
    const FILE_NAME = 'StepImportFinish';

    public function checkIfRightStep($currentStep) {
        if ($currentStep === (new \ReflectionClass($this))->getShortName()) {
            return true;
        }

        return false;
    }

    public function takeStepData()
    {
        $templateDataObj = new OnBoardTemplateData();
        $templateDataObj->setContainerClass('right-lower-center tablet-higher');
        $templateDataObj->setHeading($this->module->l('DPD on-board completed!', self::FILE_NAME));

        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('To see if everything was imported successfully check Zones/Products/Price rules tabs.', self::FILE_NAME)
        ));
        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('If you have any questions after installation please refer to the user-guide or feel free to contact your support.', self::FILE_NAME)
        ));

        $templateDataObj->setButton(new OnBoardButton(
            $this->module->l('Thank you', self::FILE_NAME),
            'center-block btn-light button-border js-dpd-hide-on-board'
        ));

        return $templateDataObj->getTemplateData();
    }

    public function takeStepAction()
    {
    }
}
