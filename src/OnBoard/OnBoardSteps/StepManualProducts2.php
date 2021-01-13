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

class StepManualProducts2 extends AbstractOnBoardStep
{
    const FILE_NAME = 'StepManualProducts2';

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

        $templateDataObj->setContainerClass('center-top products');

        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('Here you have the list of products assigned to your contract.', self::FILE_NAME)
        ));
        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('Choose one service and click edit button for configuration.', self::FILE_NAME)
        ));

        $currentProgressBarStep = Config::ON_BOARD_PROGRESS_STEP_1;

        $templateDataObj->setManualConfigProgress(
            $this->module->l(sprintf('Products: %s/%s', $currentProgressBarStep, Config::ON_BOARD_PROGRESS_BAR_PRODUCTS_STEPS), self::FILE_NAME)
        );

        $templateDataObj->setProgressBarObj(new OnBoardProgressBar(
            Config::ON_BOARD_PROGRESS_BAR_SECTIONS,
            $this->stepDataService->getCurrentProgressBarSection(),
            $currentProgressBarStep,
            'step'. $currentProgressBarStep . '-' . Config::ON_BOARD_PROGRESS_BAR_PRODUCTS_STEPS
        ));

        return $templateDataObj->getTemplateData();
    }

    public function takeStepAction()
    {
        if (Tools::isSubmit('ajax')) {
            return;
        }

        $this->stepActionService->ifNotRightControllerReverseStep(
            DPDBaltics::ADMIN_PRODUCTS_CONTROLLER,
            Config::STEP_MANUAL_PRODUCTS_0
        );

        $this->stepActionService->setManualConfigCompletedSteps(Config::ON_BOARD_PRODUCTS_PART);
    }
}
