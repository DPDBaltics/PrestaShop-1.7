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
use Invertus\dpdBaltics\OnBoard\Objects\OnBoardParagraph;
use Invertus\dpdBaltics\OnBoard\Objects\OnBoardTemplateData;

class StepMain2 extends AbstractOnBoardStep
{
    const FILE_NAME = 'StepMain2';

    public function checkIfRightStep($currentStep) {
        if ($currentStep === (new \ReflectionClass($this))->getShortName()) {
            return true;
        }

        return false;
    }

    public function takeStepData()
    {
        $templateDataObj = new OnBoardTemplateData();
        $templateDataObj->setContainerClass('right-top');

        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('To start using the module, you need to enter your credentials that were provided to your when you signed your contract with DPD.', self::FILE_NAME)
        ));

        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('Please also check that the selected country is correct for your contract.', self::FILE_NAME)
        ));
//        $templateDataObj->setParagraph(new OnBoardParagraph(
//            $this->module->l('When you will sign in, additional tabs will appear.', self::FILE_NAME)
//        ));
        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('Recap:', self::FILE_NAME),
            'on-board-text-heading'
        ));
        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('1. Enter your DPD credentials.', self::FILE_NAME),
            'on-board-pl-1 on-board-primary-color on-board-font-weight-bold'
        ));
        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('2. Check that selected country is correct.', self::FILE_NAME),
            'on-board-pl-1 on-board-primary-color on-board-font-weight-bold'
        ));
        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('3. Click "Save"', self::FILE_NAME),
            'on-board-pl-1 on-board-secondary-color on-board-font-weight-bold'
        ));

        return $templateDataObj->getTemplateData();
    }

    public function takeStepAction()
    {
        $this->stepActionService->skipLoginStepIfLoggedIn();
    }
}
