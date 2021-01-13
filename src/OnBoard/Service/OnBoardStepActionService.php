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

namespace Invertus\dpdBaltics\OnBoard\Service;

use Configuration;
use Controller;
use Cookie;
use DPDBaltics;
use Invertus\dpdBaltics\Config\Config;
use Tools;

class OnBoardStepActionService
{
    /** @var DPDBaltics */
    private $module;
    /**
     * @var Controller
     */
    private $controller;
    /**
     * @var Cookie
     */
    private $cookie;

    private $importedMainFiles = array();

    public function __construct(
        DPDBaltics $module,
        Controller $controller,
        Cookie $cookie
    ) {
        $this->module = $module;
        $this->controller = $controller;
        $this->cookie = $cookie;
    }

    public function skipLoginStepIfLoggedIn()
    {
        $username = Tools::getIsset(Config::WEB_SERVICE_USERNAME) ?
            Tools::getValue(Config::WEB_SERVICE_USERNAME) :
            null;

        $password = Tools::getIsset(Config::WEB_SERVICE_PASSWORD) ?
            Tools::getValue(Config::WEB_SERVICE_PASSWORD) :
            null;

        if (Configuration::get(Config::WEB_SERVICE_USERNAME)) {
            $username = Configuration::get(Config::WEB_SERVICE_USERNAME);
        }

        if (Configuration::get(Config::WEB_SERVICE_PASSWORD)) {
            $password = Configuration::get(Config::WEB_SERVICE_PASSWORD);
        }

        if ($username && $password) {
            Configuration::updateValue(Config::ON_BOARD_STEP, Config::STEP_MAIN_3);

            return true;
        }

        return false;
    }

    public function disableOnBoarding()
    {
        if (!Configuration::updateValue(Config::ON_BOARD_TURNED_ON, 0)) {
            return false;
        };

        return true;
    }

    public function enableOnBoarding()
    {
        if (!Configuration::updateValue(Config::ON_BOARD_TURNED_ON, 1)) {
            return false;
        };

        return true;
    }

    public function pauseOnBoarding()
    {
        if (!Configuration::updateValue(Config::ON_BOARD_PAUSE, 1)) {
            return false;
        };

        return true;
    }

    public function resumeOnBoarding()
    {
        if (!Configuration::updateValue(Config::ON_BOARD_PAUSE, 0)) {
            return false;
        };

        return true;
    }

    public function nextStepIfRightController($controller, $step)
    {
        $controller = $controller . 'Controller';

        if ($this->controller instanceof $controller) {
            $this->updateStep($step);

            return true;
        }

        return false;
    }

    public function ifNotRightControllerReverseStep($controller, $step)
    {
        $controller = $controller . 'Controller';

        if (!$this->controller instanceof $controller) {
            $this->updateStep($step);
        }
    }

    public function ifStepIsSameAsInConfigReverseStep($currentStep, $newStep)
    {
        $stepFromConfig = Configuration::get(Config::ON_BOARD_STEP);

        if ($currentStep === $stepFromConfig) {
            $this->updateStep($newStep);
        }
    }

    public function nextStep($step)
    {
        $this->updateStep($step);
    }

    public function processStepIfAllMainFilesImported()
    {
        // if all main files are imported forward on-board step.
        if (!array_diff(Config::getOnBoardImportTypes(), $this->importedMainFiles)) {
            $this->updateStep(Config::STEP_IMPORT_FINISH);

            return true;
        }

        return false;
    }

    public function unsetImportedFilesCookie()
    {
        unset($this->cookie->{Config::ON_BOARD_COOKIE_KEY});
    }

    public function checkAndAssignImportedFilesFromCookie()
    {
        if (!isset($this->cookie->{Config::ON_BOARD_COOKIE_KEY})) {
            return false;
        }

        $this->importedMainFiles = json_decode($this->cookie->{Config::ON_BOARD_COOKIE_KEY});

        return true;
    }

    public function setImportToZip()
    {
        Configuration::updateValue(Config::IMPORT_OPTION, Config::IMPORT_EXPORT_OPTION_ALL_ZIP);
    }

    public function setManualConfigCompletedSteps($completedSteps)
    {
        Configuration::updateValue(Config::ON_BOARD_MANUAL_CONFIG_CURRENT_PART, $completedSteps);
    }

    private function updateStep($newStep) {
        Configuration::updateValue(Config::ON_BOARD_STEP, $newStep);
    }
}
