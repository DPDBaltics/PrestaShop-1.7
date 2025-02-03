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


namespace Invertus\dpdBaltics\Factory;

use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Provider\CurrentCountryProvider;
use Invertus\dpdBaltics\Util\CountryUtility;
use Invertus\dpdBaltics\Util\StringUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ParcelTrackingUrlFactory
{
    /**
     * @var CurrentCountryProvider
     */
    private $countryProvider;

    public function __construct(CurrentCountryProvider $countryProvider)
    {
        $this->countryProvider = $countryProvider;
    }
    public function createTrackingUrl($cart, $parcelNumber)
    {

        $currentCountry = $this->countryProvider->getCurrentCountryIsoCode($cart);

        if (in_array($currentCountry, Config::VALID_TRACKING_URL_COUNTRIES, true)) {
            $countryIsoCode = StringUtility::toLowerCase($currentCountry);

            return "https://www.dpdgroup.com/{$countryIsoCode}/mydpd/my-parcels/track?lang={$countryIsoCode}&parcelNumber={$parcelNumber}";
        }

        return "https://www.dpdgroup.com/lt/mydpd/my-parcels/track?lang=en&parcelNumber={$parcelNumber}";
    }

    public function createTrackingUrls($cart, $parcelNumbers)
    {
        if (empty($parcelNumbers)) {
            return [];
        }
        $urls = [];

        foreach ($parcelNumbers as $parcelNumber) {
            $urls[$parcelNumber] = $this->createTrackingUrl($cart, $parcelNumber);
        }

        return $urls;
    }
}
