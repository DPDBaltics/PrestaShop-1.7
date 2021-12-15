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
use Configuration;

class StepManualZones1 extends AbstractOnBoardStep
{
    const FILE_NAME = 'StepManualZones1';

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

        $templateDataObj->setFastMoveButton(NEW OnBoardFastMoveButton(
            Config::STEP_MAIN_3,
            Config::STEP_FAST_MOVE_BACKWARD
        ));

        if ($this->stepDataService->isAtLeastOneZoneCreated()) {
            $templateDataObj->setFastMoveButton(NEW OnBoardFastMoveButton(
                Config::STEP_MANUAL_PRODUCTS_0,
                Config::STEP_FAST_MOVE_FORWARD
            ));
        }

        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('That\'s okay.', self::FILE_NAME)
        ));
        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('We will start with clean configuration.', self::FILE_NAME)
        ));

        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('First you will need to go to Zones tab and create at least one zone.', self::FILE_NAME)
        ));

        return $templateDataObj->getTemplateData();
    }

    public function takeStepAction()
    {
        if ($this->stepActionService->nextStepIfRightController(
            DPDBaltics::ADMIN_ZONES_CONTROLLER,
            Config::STEP_MANUAL_ZONES_2
        )) {
            $this->stepActionService->setManualConfigCompletedSteps(Config::ON_BOARD_ZONES_PART);
        };
    }
}
