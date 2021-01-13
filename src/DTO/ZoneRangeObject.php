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

namespace Invertus\dpdBaltics\DTO;

use Invertus\dpdBaltics\Validate\Zone\ZoneRangeValidate;

class ZoneRangeObject
{
    /**
     * @var int
     */
    public $idCountry;

    /**
     * @var bool
     */
    public $includeAll;

    /**
     * @var string
     */
    public $zipCodeFrom;

    /**
     * @var string
     */
    public $zipCodeTo;

    /**
     * @var string
     */
    public $countryName;

    /**
     * @return int
     */
    public function getIdCountry()
    {
        return $this->idCountry;
    }

    /**
     * @param int $idCountry
     */
    public function setIdCountry($idCountry)
    {
        $this->idCountry = $idCountry;
    }

    /**
     * @return bool
     */
    public function getIncludeAll()
    {
        return $this->includeAll;
    }

    /**
     * @param bool $includeAll
     */
    public function setIncludeAll($includeAll)
    {
        $this->includeAll = $includeAll;
    }

    /**
     * @return string
     */
    public function getZipCodeFrom()
    {
        return $this->zipCodeFrom;
    }

    /**
     * @param string $zipCodeFrom
     */
    public function setZipCodeFrom($zipCodeFrom)
    {
        $this->zipCodeFrom = $zipCodeFrom;
    }

    /**
     * @return string
     */
    public function getZipCodeTo()
    {
        return $this->zipCodeTo;
    }

    /**
     * @param string $zipCodeTo
     */
    public function setZipCodeTo($zipCodeTo)
    {
        $this->zipCodeTo = $zipCodeTo;
    }

    /**
     * @return string
     */
    public function getCountryName()
    {
        return $this->countryName;
    }

    /**
     * @param string $countryName
     */
    public function setCountryName($countryName)
    {
        $this->countryName = $countryName;
    }

    public function getNumericZipCodeFrom()
    {
        $zipCodeFrom = $this->getZipCodeFrom();

        $idCountry = $this->getIdCountry();

        return ZoneRangeValidate::getNumericZipCode($zipCodeFrom, $idCountry);
    }

    public function getNumericZipCodeTo()
    {
        $zipCodeTo = $this->getZipCodeTo();

        $idCountry = $this->getIdCountry();

        return ZoneRangeValidate::getNumericZipCode($zipCodeTo, $idCountry);
    }
}
