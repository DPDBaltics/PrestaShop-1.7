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

class StepImport2 extends AbstractOnBoardStep
{
    const FILE_NAME = 'StepImport2';

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

        $templateDataObj->setContainerClass('right-lower-center tablet-higher');

        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('If you want to import all in ZIP, select ZIP in import field.', self::FILE_NAME)
        ));
        $templateDataObj->setParagraph(new OnBoardParagraph(
            $this->module->l('If you want to import just a part, select which file you want to import.', self::FILE_NAME)
        ));

        $importedFilesCookieResponse = $this->stepDataService->checkAndAssignImportedFilesFromCookie();

        if ($importedFilesCookieResponse) {
            $this->stepDataService->unsetImportedFilesCookie();
        }

        $mainImportedFilesStatus = $this->stepDataService->getMainFilesImportStatus();

        if ($mainImportedFilesStatus) {
            $templateDataObj->setParagraph(new OnBoardParagraph(
                $this->module->l('Import ZIP did not have all the necessary files. Please provide correct ZIP or configure module manually.', self::FILE_NAME),
                'on-board-primary-color'
            ));

            $templateDataObj->setImportedMainFilesData($mainImportedFilesStatus);

            $templateDataObj->setButton(new OnBoardButton(
                $this->module->l('Configure DPD module manually', self::FILE_NAME),
                'center-block btn-primary js-dpd-next-step js-hide-markings',
                Config::STEP_MANUAL_ZONES_1
            ));
        }

        return $templateDataObj->getTemplateData();
    }

    public function takeStepAction()
    {
        $this->stepActionService->ifNotRightControllerReverseStep(
            DPDBaltics::ADMIN_IMPORT_EXPORT_CONTROLLER,
            Config::STEP_IMPORT_1
        );

        $this->stepActionService->checkAndAssignImportedFilesFromCookie();

        if ($this->stepActionService->processStepIfAllMainFilesImported()) {
            $this->stepActionService->unsetImportedFilesCookie();
        };
    }
}
