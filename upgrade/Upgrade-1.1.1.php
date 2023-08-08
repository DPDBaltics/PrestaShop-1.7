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
use Invertus\dpdBaltics\Infrastructure\Bootstrap\Install\ModuleTabInstaller;
use Invertus\dpdBaltics\Infrastructure\Bootstrap\Uninstall\ModuleTabUninstaller;
use Invertus\dpdBaltics\Provider\CurrentCountryProvider;

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

    /** @var CurrentCountryProvider $currentCountryProvider */
    $currentCountryProvider = $module->getService('invertus.dpdbaltics.provider.current_country_provider');
    $countryCode = $currentCountryProvider->getCurrentCountryIsoCode();

    Configuration::updateValue(Config::DPD_PARCEL_IMPORT_COUNTRY_SELECTOR, Country::getByIso($countryCode));

    return $result;
}
