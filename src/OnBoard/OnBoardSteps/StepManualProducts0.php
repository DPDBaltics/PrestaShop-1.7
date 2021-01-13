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

class StepManualProducts0 extends AbstractOnBoardStep
{
    const FILE_NAME = 'StepManualProducts0';

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

        $templateDataObj->setFastMoveButton(NEW OnBoardFastMoveButton(
            Config::STEP_MANUAL_PRICE_RULES_0,
            Config::STEP_FAST_MOVE_FORWARD
        ));

        $templateDataObj->setContainerClass('center-top');

        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('If you are ready, let\'s move on to Products configuration.', self::FILE_NAME)
        ));

        return $templateDataObj->getTemplateData();
    }

    public function takeStepAction()
    {
        if ($this->stepActionService->nextStepIfRightController(
            DPDBaltics::ADMIN_PRODUCTS_CONTROLLER,
            Config::STEP_MANUAL_PRODUCTS_2
        )) {
            $this->stepActionService->setManualConfigCompletedSteps(Config::ON_BOARD_PRODUCTS_PART);
        };
    }
}
