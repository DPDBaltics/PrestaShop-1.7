<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace Invertus\dpdBaltics\Infrastructure\Bootstrap\Uninstall;

use Invertus\dpdBaltics\Infrastructure\Bootstrap\Exception\CouldNotUninstallModule;

class Uninstaller implements UninstallerInterface
{
    private $moduleTabUninstaller;
    private $legacyInstaller;

    public function __construct(
        ModuleTabUninstaller $moduleTabUninstaller,
        \Invertus\dpdBaltics\Install\Installer $legacyInstaller  //TODO remove it at some point when refactoring happens, currently just using it via adapter
    ) {
        $this->moduleTabUninstaller = $moduleTabUninstaller;
        $this->legacyInstaller = $legacyInstaller;
    }

    /** {@inheritDoc} */
    public function init()
    {
        try {
            $this->moduleTabUninstaller->init();
            $this->legacyInstaller->uninstall();
        } catch (CouldNotUninstallModule $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw CouldNotUninstallModule::unknownError($exception);
        }
    }
}
