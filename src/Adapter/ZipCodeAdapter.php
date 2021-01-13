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

use Country;
use Tools;
use Validate;

/**
 * Class DPDZipCodeAdapter
 * @package src\Adapter
 */
class ZipCodeAdapter
{
    /**
     * @var Country
     */
    private $country;
    /**
     * @var string
     */
    private $zipCode;

    /**
     * DPDZipCodeAdapter constructor.
     *
     * @param Country $country
     * @param string $zipCode
     */
    public function __construct(Country $country, $zipCode)
    {
        $this->country = $country;
        $this->zipCode = $zipCode;
    }

    /**
     * Gets zip code appended with country code or the dash.
     *
     * @return string
     */
    public function getWithCountryCode()
    {
        $formatForDash = str_replace('C', '', $this->country->zip_code_format);
        $dashPosition = $this->getDashPosition($this->country->zip_code_format);
        $isDashFound = '-' === Tools::substr($this->zipCode, $dashPosition, 1);

        $zipCode = $this->zipCode;
        if (!$isDashFound) {
            $zipCode = $this->getZipCodeWithModifiedDash($this->zipCode, $formatForDash);
        }

        return $this->getZipCodeWithModifiedCountryIso($zipCode, $this->country->zip_code_format);
    }

    private function getZipCodeWithModifiedDash($zipCode, $format)
    {
        $dashPosition = $this->getDashPosition($format, true);
        $isDash = false !== $dashPosition;

        if ($isDash) {
            $zipCode = $this->getWithAppendedDash(
                $zipCode,
                $dashPosition
            );
        }

        $format = $this->getFormatWithoutDashes($format);

        if (false !== $this->getDashPosition($format, $ignoreCountryLength = true)) {
            $zipCode = $this->getZipCodeWithModifiedDash($zipCode, $format);
        }

        return $zipCode;
    }

    private function isCountryCodesMatches($zipFormat)
    {
        $isoCodePosition = $this->getCountryCodePosition($zipFormat);
        $countryCodeLength = Tools::strlen($this->country->iso_code);
        $expectedCountryCode = Tools::substr($this->zipCode, $isoCodePosition, $countryCodeLength);

        $idCountry = Country::getByIso($expectedCountryCode);
        $expectedCountry = new Country($idCountry);
        return Tools::strtoupper($expectedCountry->iso_code) === Tools::strtoupper($this->country->iso_code);
    }

    private function getCountryCodePosition($zipFormat)
    {
        return Tools::strpos(Tools::strtoupper($zipFormat), 'C');
    }

    private function getWithAppendedCountryCode($zipCode, $isoCodePosition)
    {
        return substr_replace(
            $zipCode,
            $this->country->iso_code,
            $isoCodePosition,
            0
        );
    }

    private function getWithAppendedDash($zipCode, $dashPosition)
    {
        return substr_replace(
            $zipCode,
            '-',
            $dashPosition,
            0
        );
    }

    private function getDashPosition(
        $zipFormat,
        $ignoreCountryLength = false
    ) {
        $isoLength = Tools::strlen($this->country->iso_code);

        $format = $zipFormat;
        if ($isoLength > 1 && !$ignoreCountryLength) {
            $isoCodeReal = str_repeat('C', $isoLength);
            $format = str_replace('C', $isoCodeReal, $format);
        }

        return Tools::strpos(Tools::strtoupper($format), '-');
    }

    private function getFormatWithoutDashes($zipFormat)
    {
        $dashPosition = $this->getDashPosition(
            $zipFormat,
            $ignoreCountryLength = true
        );

        if (false !== $dashPosition) {
            $zipFormat = substr_replace(
                $zipFormat,
                '*',
                $dashPosition,
                1
            );
        }

        return $zipFormat;
    }

    private function getFormatWithoutIsoCodes($zipFormat)
    {
        $isoCodePosition = $this->getCountryCodePosition($zipFormat);

        if (false !== $isoCodePosition) {
            $zipFormat = substr_replace(
                $zipFormat,
                '',
                $isoCodePosition,
                1
            );

            $isoCodeLength = Tools::strlen($this->country->iso_code);

            $values = str_repeat('*', $isoCodeLength);
            $zipFormat = substr_replace(
                $zipFormat,
                $values,
                $isoCodePosition,
                0
            );
        }

        return $zipFormat;
    }

    private function getZipCodeWithModifiedCountryIso($zipCode, $format)
    {
        $isoCodePosition = $this->getCountryCodePosition($format);
        $isCountryCode = false !== $isoCodePosition;


        $countryCodeLength = Tools::strlen($this->country->iso_code);
        $expectedIso = Tools::substr($this->zipCode, $isoCodePosition, $countryCodeLength);

        $isAppendAction = false;
        if ($isCountryCode && !Validate::isLangIsoCode($expectedIso)) {
            $isAppendAction = true;
        }

        if ($isCountryCode && !$isAppendAction && $this->isCountryCodesMatches($format)) {
            $isAppendAction = false;
        }

        if ($isAppendAction) {
            $zipCode = $this->getWithAppendedCountryCode(
                $zipCode,
                $isoCodePosition
            );
        }

        $format = $this->getFormatWithoutIsoCodes($format);

        if (false !== $this->getCountryCodePosition($format)) {
            $zipCode = $this->getZipCodeWithModifiedCountryIso($zipCode, $format);
        }

        return $zipCode;
    }
}
