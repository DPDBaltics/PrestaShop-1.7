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

use Invertus\dpdBaltics\OnBoard\AbstractOnBoardStep;
use Invertus\dpdBaltics\OnBoard\Objects\OnBoardButton;
use Invertus\dpdBaltics\OnBoard\Objects\OnBoardParagraph;
use Invertus\dpdBaltics\OnBoard\Objects\OnBoardTemplateData;

class StepManualConfigFinish extends AbstractOnBoardStep
{
    const FILE_NAME = 'StepManualConfigFinish';

    public function checkIfRightStep($currentStep) {
        if ($currentStep === (new \ReflectionClass($this))->getShortName()) {
            return true;
        }

        return false;
    }

    public function takeStepData()
    {
        $templateDataObj = new OnBoardTemplateData();
        $templateDataObj->setContainerClass('center-top');

        $templateDataObj->setHeading($this->module->l('Congratulation!', self::FILE_NAME));

        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('You have finished basic module configuration.', self::FILE_NAME)
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
