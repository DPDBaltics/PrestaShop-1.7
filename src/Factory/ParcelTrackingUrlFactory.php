<?php

namespace Invertus\dpdBaltics\Factory;

use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Provider\CurrentCountryProvider;
use Invertus\dpdBaltics\Util\CountryUtility;

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
    public function createTrackingUrl($order, $parcelNumber)
    {

        $currentCountry = $this->countryProvider->getCurrentCountryIsoCode();

        if (in_array($currentCountry, Config::VALID_TRACKING_URL_COUNTRIES, true)) {
            $countryIsoCode = CountryUtility::toLowerCase($currentCountry);

            return "https://www.dpdgroup.com/{$countryIsoCode}/mydpd/tmp/basicsearch?lang={$countryIsoCode}&parcel_id={$parcelNumber}";
        }

        return "https://www.dpdgroup.com/lt/mydpd/tmp/basicsearch?lang=en&parcel_id={$parcelNumber}";
    }
}