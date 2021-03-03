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
function upgrade_module_1_1_1(DPDBaltics $module)
{
    $db = Db::getInstance();
    $dbQuery = new DbQuery();
    $dbQuery->select('id_tab');
    $dbQuery->from('tab');
    $dbQuery->where("`module` = '" . pSQL($module->name) . "'");
    $tabs = $db->executeS($dbQuery);
    foreach ($tabs as $tab) {
        $tabClass = new TabCore($tab['id_tab']);
        $tabClass->delete();
    }

    /** @var TabFactory $tabFactory */
    $tabFactory = $module->getModuleContainer(TabFactory::class);

    $tabsInstaller = new TabsInstaller($tabFactory->getTabsCollection(), $module->name);
    if (!$tabsInstaller->installTabs()) {
        return false;
    };

    /** @var CurrentCountryProvider $currentCountryProvider */
    $currentCountryProvider = $this->module->getModuleContainer(CurrentCountryProvider::class);
    $countryCode = $currentCountryProvider->getCurrentCountryIsoCode();

    Configuration::updateValue(Config::DPD_PARCEL_IMPORT_COUNTRY_SELECTOR, Country::getByIso($countryCode));

    return true;
}
