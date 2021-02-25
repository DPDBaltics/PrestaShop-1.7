<?php

namespace Invertus\dpdBaltics\Util;

use Configuration;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Provider\CurrentCountryProvider;
use PrestaShop\PrestaShop\Adapter\Country\CountryDataProvider;

class CountryUtility
{
    public static function isEstonia()
    {
        $module = \Module::getInstanceByName('dpdbaltics');

        /** @var CurrentCountryProvider $countryProvider */
        $countryProvider = $module->getModuleContainer(CurrentCountryProvider::class);

        return $countryProvider->getCurrentCountryIsoCode() === Config::ESTONIA_ISO_CODE;
    }
}
