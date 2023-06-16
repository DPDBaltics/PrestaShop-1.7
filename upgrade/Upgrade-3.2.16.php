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

use Invertus\dpdBaltics\Infrastructure\Bootstrap\Install\ModuleTabInstaller;
use Invertus\dpdBaltics\Infrastructure\Bootstrap\Uninstall\ModuleTabUninstaller;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @return bool
 */
function upgrade_module_3_2_16(DPDBaltics $module)
{
    /** @var ModuleTabUninstaller $moduleTabUninstaller */
    $moduleTabUninstaller = $module->getService(ModuleTabUninstaller::class);

    /** @var ModuleTabInstaller $moduleTabInstaller */
    $moduleTabInstaller = $module->getService(ModuleTabInstaller::class);

    $result = true;

    try {
        $moduleTabUninstaller->init();
        $moduleTabInstaller->init();
    } catch (Exception $exception) {
        $result &= false;
    }

    return $result;
}
