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

use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\OnBoard\AbstractOnBoardStep;
use Invertus\dpdBaltics\OnBoard\Objects\OnBoardButton;
use Invertus\dpdBaltics\OnBoard\Objects\OnBoardParagraph;
use Invertus\dpdBaltics\OnBoard\Objects\OnBoardTemplateData;

class StepMain3 extends AbstractOnBoardStep
{
    const FILE_NAME = 'StepMain3';

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

        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('Do you have configuration ZIP?', self::FILE_NAME)
        ));
        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('(It can be provided by your DPD agent or from the previous configuration)', self::FILE_NAME)
        ));

        $templateDataObj->setButton(new OnBoardButton(
            $this->module->l('I have a Zip', self::FILE_NAME),
            'pull-left btn-light button-border small-button js-dpd-next-step',
            Config::STEP_IMPORT_1
        ));
        $templateDataObj->setButton(new OnBoardButton(
            $this->module->l('Start manual on-board guide', self::FILE_NAME),
            'pull-right btn-primary js-dpd-next-step',
            Config::STEP_MANUAL_ZONES_1
        ));

        return $templateDataObj->getTemplateData();
    }

    public function takeStepAction()
    {
    }
}
