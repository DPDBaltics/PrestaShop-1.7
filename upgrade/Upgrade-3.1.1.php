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

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @return bool
 */
function upgrade_module_3_1_1(DPDBaltics $module)
{
    $module->registerHook('actionOrderGridDefinitionModifier');
    $module->registerHook('displayAdminOrderTabContent');
    $module->unregisterHook('actionDispatcher');

    return true;
}
