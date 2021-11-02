<?php

namespace Invertus\dpdBaltics\Factory;

use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Provider\CurrentCountryProvider;
use Invertus\dpdBaltics\Util\CountryUtility;
use Invertus\dpdBaltics\Util\StringUtility;

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
