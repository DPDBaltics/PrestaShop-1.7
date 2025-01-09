<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of INVERTUS, UAB
 */
namespace Invertus\dpdBaltics\Infrastructure\Bootstrap\Install;

use Invertus\dpdBaltics\Infrastructure\Bootstrap\Exception\CouldNotInstallModule;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Installer implements InstallerInterface
{
    private $moduleTabInstaller;
    private $legacyInstaller;

    public function __construct(
        ModuleTabInstaller $moduleTabInstaller,
        \Invertus\dpdBaltics\Install\Installer $legacyInstaller //TODO remove it at some point when refactoring happens, currently just using it via adapter
    ) {
        $this->moduleTabInstaller = $moduleTabInstaller;
        $this->legacyInstaller = $legacyInstaller;
    }

    /** {@inheritDoc} */
    public function init()
    {
        try {
            $this->moduleTabInstaller->init();
            $this->legacyInstaller->install();
        } catch (CouldNotInstallModule $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw CouldNotInstallModule::unknownError($exception);
        }
    }
}