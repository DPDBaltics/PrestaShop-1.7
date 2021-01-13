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

namespace Invertus\dpdBaltics\Adapter;

use Address;
use Country;
use DPDBaltics;
use Tools;

/**
 * Class DPDAddressAdapter
 */
class AddressAdapter
{
    /**
     * @var Address
     */
    private $address;

    /**
     * DPDAddressAdapter constructor.
     * @param Address|null $address
     */
    public function __construct(Address $address = null)
    {
        $this->address = $address;
    }

    /**
     * Removes country code from zip code
     *
     * @return string
     */
    public function getZipCode()
    {
        $country = new Country($this->address->id_country);

        return $this->getFormattedZipCode($country, $this->address->postcode);
    }

    /**
     * Modifies given zip code according to given country
     *
     * @param $idCountry
     * @param $zipCode
     *
     * @return string
     */
    public function getZipCodeByCountry($idCountry, $zipCode)
    {
        $country = new Country($idCountry);

        return $this->getFormattedZipCode($country, $zipCode);
    }

    /**
     * gets address with modified zip code
     * @return Address
     */
    public function getAddress()
    {
        $this->address->postcode = $this->getZipCode();
        return $this->address;
    }

    private function getCountryCodePosition(Country $country)
    {
        return Tools::strpos(Tools::strtoupper($country->zip_code_format), 'C');
    }

    private function getFormattedZipCode(Country $country, $postCode)
    {
        $countryCodePosition = $this->getCountryCodePosition($country);

        $postCode = preg_replace("/[^a-zA-Z0-9]+/", "", $postCode);
        // If C doesn't exist in zip code format - don't modify the zip code
        if (false === $countryCodePosition) {
            return $postCode;
        }

        $countryCodeLength = Tools::strlen($country->iso_code);
        $countryCode = Tools::substr($postCode, $countryCodePosition, $countryCodeLength);

        // If stripped country code doesn't match country iso code - something went wrong.
        // Possibly we didn't need to strip it at all, that's why return original postcode.
        if (Tools::strtoupper($countryCode) !== Tools::strtoupper($country->iso_code)) {
            return $postCode;
        }

        // Remove iso code in the correct position of the zip code
        $zipCode = substr_replace(
            $postCode,
            '',
            $countryCodePosition,
            Tools::strlen($country->iso_code)
        );

        // Remove any leading or trailing dashes or spaces after iso code removal
        $zipCode = trim($zipCode, '- ');

        return $zipCode;
    }

    /** Changes zip code format from pudo service to the one used in prestashop as based on country and returns it*/
    public function getFormattedZipCodePudoToPrestashop($iso, $zipCode)
    {
        $zipCode = preg_replace("/[^a-zA-Z0-9]+/", "", $zipCode);
        $country = new Country(Country::getByIso($iso));
        $formattedZipCode = $zipCode;
        $isoAdded = false;
        $chars = $chars = str_split($country->zip_code_format);
        foreach ($chars as $key => $char) {
            if ($isoAdded) {
                $key++;
            }
            if ($char == 'C') {
                $isoAdded = true;
                $formattedZipCode = substr_replace($formattedZipCode, $iso, $key, 0);
            }
            if ($char == " " || $char == "-") {
                $formattedZipCode = substr_replace($formattedZipCode, $char, $key, 0);
            }
        }

        return $formattedZipCode;
    }
}
