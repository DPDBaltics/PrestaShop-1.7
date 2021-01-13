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
use Invertus\dpdBaltics\OnBoard\Objects\OnBoardFastMoveButton;
use Invertus\dpdBaltics\OnBoard\Objects\OnBoardParagraph;
use Invertus\dpdBaltics\OnBoard\Objects\OnBoardTemplateData;
use Tools;

class StepManualProducts1 extends AbstractOnBoardStep
{
    const FILE_NAME = 'StepManualProducts1';

    public function checkIfRightStep($currentStep) {
        if ($currentStep === (new \ReflectionClass($this))->getShortName()) {
            return true;
        }

        return false;
    }

    public function takeStepData()
    {
        $templateDataObj = new OnBoardTemplateData();

        $templateDataObj->setFastMoveButton(NEW OnBoardFastMoveButton(
            Config::STEP_MANUAL_ZONES_0,
            Config::STEP_FAST_MOVE_BACKWARD
        ));

        if ($this->stepDataService->isAtLeastOneProductActive()) {
            $templateDataObj->setFastMoveButton(NEW OnBoardFastMoveButton(
                Config::STEP_MANUAL_PRICE_RULES_0,
                Config::STEP_FAST_MOVE_FORWARD
            ));
        }

        $templateDataObj->setContainerClass('right-lower-center');

        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('You have no products created yet.', self::FILE_NAME)
        ));

        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('Click "Synchronise products" button to import your account contracts.', self::FILE_NAME),
            'on-board-font-weight-bold'
        ));

        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('Top toolbar "Synchronise contracts" button is always visible in Contracts tab. You can update your contracts at any time.', self::FILE_NAME)
        ));

        return $templateDataObj->getTemplateData();
    }

    public function takeStepAction()
    {
        $this->stepActionService->ifNotRightControllerReverseStep(
            DPDBaltics::ADMIN_PRODUCTS_CONTROLLER,
            Config::STEP_MANUAL_PRODUCTS_0
        );

        $this->stepActionService->setManualConfigCompletedSteps(Config::ON_BOARD_PRODUCTS_PART);
    }
}
