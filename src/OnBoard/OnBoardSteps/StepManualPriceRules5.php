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

class StepManualPriceRules5 extends AbstractOnBoardStep
{
    const FILE_NAME = 'StepManualPriceRules5';

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
            Config::STEP_MANUAL_PRODUCTS_0,
            Config::STEP_FAST_MOVE_BACKWARD
        ));

        if ($this->stepDataService->isAtLeastOnePriceRuleCreated()) {
            $templateDataObj->setFastMoveButton(NEW OnBoardFastMoveButton(
                Config::STEP_MANUAL_CONFIG_FINISH,
                Config::STEP_FAST_MOVE_FORWARD
            ));
        }

        $templateDataObj->setContainerClass('right-center price-rules');

        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('By default all configured carriers will be selected, however you can change that, if you want the rule to apply only to one or few carriers, you can unselect other carriers.', self::FILE_NAME)
        ));

        $templateDataObj->setButton(new OnBoardButton(
            $this->module->l('Next', self::FILE_NAME),
            'pull-right btn-light button-border js-dpd-next-step',
            Config::STEP_MANUAL_PRICE_RULES_6
        ));

        $currentProgressBarStep = Config::ON_BOARD_PROGRESS_STEP_5;

        $templateDataObj->setManualConfigProgress(
            $this->module->l(sprintf('Price rules: %s/%s', $currentProgressBarStep, Config::ON_BOARD_PROGRESS_PRICE_RULES_STEPS), self::FILE_NAME)
        );

        $templateDataObj->setProgressBarObj(new OnBoardProgressBar(
            Config::ON_BOARD_PROGRESS_BAR_SECTIONS,
            $this->stepDataService->getCurrentProgressBarSection(),
            $currentProgressBarStep,
            'step'. $currentProgressBarStep . '-' . Config::ON_BOARD_PROGRESS_PRICE_RULES_STEPS
        ));

        return $templateDataObj->getTemplateData();
    }

    public function takeStepAction()
    {
        if (Tools::isSubmit('ajax')) {
            return;
        }

        $this->stepActionService->ifNotRightControllerReverseStep(
            DPDBaltics::ADMIN_PRICE_RULES_CONTROLLER,
            Config::STEP_MANUAL_PRICE_RULES_0
        );

        /** If current step is same as set in Configuration at this point it means that page was reloaded */
        $this->stepActionService->ifStepIsSameAsInConfigReverseStep(
            Config::STEP_MANUAL_PRICE_RULES_5,
            Config::STEP_MANUAL_PRICE_RULES_2
        );
    }
}
