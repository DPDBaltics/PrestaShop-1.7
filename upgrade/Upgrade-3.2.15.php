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

use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Factory\TabFactory;
use Invertus\dpdBaltics\Infrastructure\Bootstrap\Install\ModuleTabInstaller;
use Invertus\dpdBaltics\Install\Installer;
use Invertus\dpdBaltics\Provider\CurrentCountryProvider;
use Invertus\psModuleTabs\Object\Tab;
use Invertus\psModuleTabs\Object\TabsCollection;
use Invertus\psModuleTabs\Service\TabsInitializer;
use Invertus\psModuleTabs\Service\TabsInstaller;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @return bool
 *
 * @throws PrestaShopDatabaseException
 * @throws PrestaShopException
 */
function upgrade_module_3_2_15(DPDBaltics $module)
{
    $result = true;

    $dbQuery = new DbQuery();
    $dbQuery->select('id_tab');
    $dbQuery->from('tab');
    $dbQuery->where("`module` = '" . pSQL($module->name) . "'");
    $tabs =  Db::getInstance()->executeS($dbQuery);
    foreach ($tabs as $tab) {
        $tabClass = new TabCore($tab['id_tab']);
        $tabClass->delete();
    }

    /** @var ModuleTabInstaller $moduleTabInstaller */
    $moduleTabInstaller = $module->getService(ModuleTabInstaller::class);

    try {
        $moduleTabInstaller->init();
    } catch (Exception $exception) {
        $result &= false;
    }

    return $result;
}