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
use Invertus\dpdBaltics\OnBoard\Objects\OnBoardProgressBar;
use Invertus\dpdBaltics\OnBoard\Objects\OnBoardTemplateData;
use Tools;

class StepManualZones3 extends AbstractOnBoardStep
{
    const FILE_NAME = 'StepManualZones3';

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

        $templateDataObj->setContainerClass('right-top');

        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('Enter name of the zone, it should be unique and easy understandable for you, because it will be used every there for configuration purposes.', self::FILE_NAME)
        ));

        $templateDataObj->setButton(new OnBoardButton(
            $this->module->l('Next', self::FILE_NAME),
            'pull-right btn-light button-border js-dpd-next-step',
            Config::STEP_MANUAL_ZONES_4,
            '.js-dpd-zone-name'
        ));

        $currentProgressBarStep = Config::ON_BOARD_PROGRESS_STEP_2;

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
        if (Tools::isSubmit('ajax')) {
            return;
        }

        $this->stepActionService->ifNotRightControllerReverseStep(
            DPDBaltics::ADMIN_ZONES_CONTROLLER,
            Config::STEP_MANUAL_ZONES_0
        );

        if (!Tools::getIsset('adddpd_zone') &&
            !Tools::getIsset('updatedpd_zone') &&
            !Tools::getIsset('submitAdddpd_zone')
        ) {
            $this->stepActionService->nextStep(Config::STEP_MANUAL_ZONES_2);
        }
    }
}
