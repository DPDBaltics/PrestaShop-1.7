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
        $countryProvider = $module->getModuleContainer('invertus.dpdbaltics.provider.current_country_provider');

        return $countryProvider->getCurrentCountryIsoCode() === Config::ESTONIA_ISO_CODE;
    }
}
