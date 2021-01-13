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

class StepMain1 extends AbstractOnBoardStep
{
    const FILE_NAME = 'StepMain1';

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
        $templateDataObj->setHeading($this->module->l('Hi there,', self::FILE_NAME));

        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('Thank you for installing DPD module', self::FILE_NAME)
        ));
        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('I am here to guide you via module configuration process. If you have any questions after installation please refer to the user-guide or feel free to contact your support agent', self::FILE_NAME)
        ));

        $templateDataObj->setButton(new OnBoardButton(
            $this->module->l('I know what I am doing', self::FILE_NAME),
            'pull-left btn-light button-border js-dpd-stop-on-board'
        ));
        $templateDataObj->setButton(new OnBoardButton(
            $this->module->l('Please guide me', self::FILE_NAME),
            'pull-right btn-primary js-dpd-next-step',
            Config::STEP_MAIN_2
        ));

        return $templateDataObj->getTemplateData();
    }

    public function takeStepAction()
    {
    }
}
