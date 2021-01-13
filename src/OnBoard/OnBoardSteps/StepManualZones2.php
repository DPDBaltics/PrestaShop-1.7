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
use Invertus\dpdBaltics\OnBoard\Objects\OnBoardProgressBar;
use Invertus\dpdBaltics\OnBoard\Objects\OnBoardTemplateData;
use Tools;

class StepManualZones2 extends AbstractOnBoardStep
{
    const FILE_NAME = 'StepManualZones2';

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
            Config::STEP_MAIN_3,
            Config::STEP_FAST_MOVE_BACKWARD
        ));

        if ($this->stepDataService->isAtLeastOneZoneCreated()) {
            $templateDataObj->setFastMoveButton(NEW OnBoardFastMoveButton(
                Config::STEP_MANUAL_PRODUCTS_0,
                Config::STEP_FAST_MOVE_FORWARD
            ));
        }

        $templateDataObj->setContainerClass('center-top');

        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('To create a zone you need to click plus sign', self::FILE_NAME)
        ));

        $currentProgressBarStep = Config::ON_BOARD_PROGRESS_STEP_1;

        $templateDataObj->setManualConfigProgress(
            $this->module->l(sprintf('Zones: %s/%s', $currentProgressBarStep, Config::ON_BOARD_PROGRESS_BAR_ZONES_STEPS), self::FILE_NAME)
        );

        $templateDataObj->setProgressBarObj(new OnBoardProgressBar(
            Config::ON_BOARD_PROGRESS_BAR_SECTIONS,
            $this->stepDataService->getCurrentProgressBarSection(),
            $currentProgressBarStep,
            'step'. $currentProgressBarStep . '-' . Config::ON_BOARD_PROGRESS_BAR_ZONES_STEPS
        ));

        return $templateDataObj->getTemplateData();
    }

    public function takeStepAction()
    {
        $this->stepActionService->ifNotRightControllerReverseStep(
            DPDBaltics::ADMIN_ZONES_CONTROLLER,
            Config::STEP_MANUAL_ZONES_0
        );

        $this->stepActionService->setManualConfigCompletedSteps(Config::ON_BOARD_ZONES_PART);

        if (Tools::getIsset('adddpd_zone') || Tools::getIsset('updatedpd_zone')) {
            $this->stepActionService->nextStep(Config::STEP_MANUAL_ZONES_3);
        }
    }
}
