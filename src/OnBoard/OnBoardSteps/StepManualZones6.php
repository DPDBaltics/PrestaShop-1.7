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

use Configuration;
use DPDBaltics;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\OnBoard\AbstractOnBoardStep;
use Invertus\dpdBaltics\OnBoard\Objects\OnBoardFastMoveButton;
use Invertus\dpdBaltics\OnBoard\Objects\OnBoardParagraph;
use Invertus\dpdBaltics\OnBoard\Objects\OnBoardProgressBar;
use Invertus\dpdBaltics\OnBoard\Objects\OnBoardTemplateData;

class StepManualZones6 extends AbstractOnBoardStep
{
    const FILE_NAME = 'StepManualZones6';

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
            $this->module->l('Good job!', self::FILE_NAME)
        ));
        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('You have created your first zone.', self::FILE_NAME)
        ));
        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('If you need more zones, feel free to repeat the step you just did.', self::FILE_NAME)
        ));

        $isLatvianService = Configuration::get(Config::WEB_SERVICE_COUNTRY) === Config::LATVIA_ISO_CODE;
        $isLithuaniaService = Configuration::get(Config::WEB_SERVICE_COUNTRY) === Config::LITHUANIA_ISO_CODE;
        if ($isLatvianService || $isLithuaniaService) {
            $templateDataObj->setParagraph(new OnBoardParagraph(
                $this->module->l('You can also choose to import zones automatically. We have prepared standard zones for Latvia and Lithuania. You can import them later in Import/Export tab.', self::FILE_NAME)
            ));
        }

        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('If you are ready, let\'s move on to Products configuration', self::FILE_NAME)
        ));

        $currentProgressBarStep = Config::ON_BOARD_PROGRESS_STEP_5;

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
        if ($this->stepActionService->nextStepIfRightController(
            DPDBaltics::ADMIN_PRODUCTS_CONTROLLER,
            Config::STEP_MANUAL_PRODUCTS_2
        )) {
            $this->stepActionService->setManualConfigCompletedSteps(Config::ON_BOARD_PRODUCTS_PART);
        };
    }
}
