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

namespace Invertus\dpdBaltics\Validate\Zone;

use Country;
use DPDBaltics;
use DPDZone;
use Invertus\dpdBaltics\Adapter\AddressAdapter;
use Invertus\dpdBaltics\Adapter\ZipCodeAdapter;
use Invertus\dpdBaltics\Collection\ZoneRangeObjectCollection;
use Invertus\dpdBaltics\DTO\ZoneRangeObject;
use Invertus\dpdBaltics\Exception\ZoneValidateException;
use Invertus\dpdBaltics\Repository\DPDZoneRepository;
use Language;
use PrestaShopDatabaseException;
use PrestaShopException;
use Tools;
use Validate;

/**
 * Class DPDZoneRangeValidate
 */
class ZoneRangeValidate
{
    /**
     * @var DPDBaltics
     */
    private $module;

    /**
     * @var array
     */
    public $errors = [];
    /**
     * @var Language
     */
    private $language;

    /**
     * DPDZoneRangeValidate constructor.
     * @param DPDBaltics $module
     */
    public function __construct(DPDBaltics $module, Language $language)
    {
        $this->module = $module;
        $this->language = $language;
    }

    /**
     * Goes trough all validation and returns zoneRanges, if not validations fail returns error
     *
     * @param $zoneId
     * @param $zoneName
     * @param $zoneRanges
     *
     * @throws ZoneValidateException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function validateZoneRanges($zoneId, $zoneName, $zoneRanges)
    {
        if (!$this->validateAndFormatZoneRanges($zoneRanges) ||
            !$this->validateZoneRangesOverlap($zoneRanges) ||
            !$this->validateZoneName($zoneName, $zoneId)
        ) {
            if (!empty($this->errors[0])) {
                throw new ZoneValidateException($this->errors[0]);
            }
        }
    }

    /**
     * Check if zone ranges are valid
     *
     * @param ZoneRangeObjectCollection $zoneRanges
     *
     * @return bool
     */
    public function validateZoneRangesOverlap(ZoneRangeObjectCollection $zoneRanges)
    {
        $countryRanges = [];

        foreach ($zoneRanges as $zoneRange) {
            /**
             * @var ZoneRangeObject $zoneRange
             */
            $zipFrom = $zoneRange->getZipCodeFrom();
            $zipTo = $zoneRange->getZipCodeTo();
            $idCountry = (int) $zoneRange->getIdCountry();
            $countryName = $zoneRange->getCountryName();
            $includeAllRanges = (bool) $zoneRange->getIncludeAll();

            $zipString = $includeAllRanges ?
                $this->module->l('All ranges') :
                sprintf('%s - %s', $zipFrom, $zipTo);

            $range = [
                'from' => $zipFrom,
                'to' => $zipTo,
                'all' => $includeAllRanges,
            ];

            if (isset($countryRanges[$idCountry])) {
                if (!$this->isRangeNotOverlapping($countryRanges[$idCountry], $range, $idCountry)) {
                    $this->errors[] = sprintf(
                        $this->module->l('Range %s (%s) is overlapping other %s\'s range(s)'),
                        $countryName,
                        $zipString,
                        $countryName
                    );

                    return false;
                }
            }

            $countryRanges[$idCountry][] = [
                'zip_code_from' => $zipFrom,
                'zip_code_to' => $zipTo,
                'include_all_zip_codes' => $includeAllRanges,
                'id_country' => $idCountry,
            ];
        }

        return true;
    }

    /**
     * Check if zone name is valid
     *
     * @param $zoneName
     * @param $zoneId
     *
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function validateZoneName($zoneName, $zoneId)
    {
        if (DPDZone::MIN_NAME_LENGTH > Tools::strlen($zoneName)) {
            $this->errors[] =
                sprintf($this->module->l('Zone name must be at least %d characters length'), DPDZone::MIN_NAME_LENGTH);

            return false;
        }

        $exclude = [];

        if (Validate::isUnsignedId($zoneId) && Validate::isLoadedObject(new DPDZone($zoneId))) {
            $exclude[] = $zoneId;
        }

        /** @var DPDZoneRepository $zoneRepository */
        $zoneRepository = $this->module->getModuleContainer()->get('invertus.dpdbaltics.repository.dpdzone_repository');
        $foundZoneId = $zoneRepository->getByName($zoneName, $exclude);

        if ($foundZoneId) {
            $this->errors[] = sprintf($this->module->l('Zone with name %s already exists'), $zoneName);

            return false;
        }

        return true;
    }

    /**
     * Checks if given range ($currentRange) is not overlapping with given array of ranges
     *
     * @param array $ranges       Current zone ranges
     * @param array $currentRange Range
     *
     * @return bool TRUE if $range is not overlapping with other ranges (valid), FALSE otherwise
     */
    public function isRangeNotOverlapping(array $ranges, array $currentRange, $idCountry)
    {
        // If no ranges exists, then current range is valid
        if (!$ranges) {
            return true;
        }

        // If current range includes all zips
        // and some ranges already exists
        // then range is invalid
        if ($currentRange['all']) {
            return false;
        }

        // If one of ranges includes all zips
        // Then no range can be created
        foreach ($ranges as $range) {
            if ($range['include_all_zip_codes']) {
                return false;
            }
        }

        // Weights of current range "from" and "to" zip codes
        $currentRangeFromWeight = self::getZipCodeWeight($currentRange['from'], $idCountry);
        $currentRangeToWeight = self::getZipCodeWeight($currentRange['to'], $idCountry);

        foreach ($ranges as $range) {
            $fromRange = $range['zip_code_from'];
            $toRange = $range['zip_code_to'];
            $fromWeight = self::getZipCodeWeight($fromRange, $idCountry);
            $toWeight = self::getZipCodeWeight($toRange, $idCountry);

            // If in front of range
            if ($currentRangeFromWeight < $fromWeight &&
                (!$currentRange['to'] || $currentRangeToWeight < $fromWeight)
            ) {
                continue;
            }

            // If behind range
            if ($currentRangeFromWeight > $toWeight &&
                (!$currentRange['to'] || $currentRangeToWeight > $toWeight)
            ) {
                continue;
            }

            return false;
        }

        return true;
    }

    /**
     * Checks if $zipCode is between $from and $to (in range)
     *
     * @param $zipCode
     * @param $from
     * @param $to
     * @param $idCountry
     *
     * @return
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function isZipCodeInRange($zipCode, $from, $to, $idCountry)
    {
        // If either of given zip codes is not valid - return false
        if (!self::isZipCode($from, $idCountry) ||
            !self::isZipCode($to, $idCountry, true) ||
            !self::isZipCode($zipCode, $idCountry)) {
            return false;
        }

        // If FROM range is bigger than zip code - return false
        if (self::getZipCodeWeight($from, $idCountry) > self::getZipCodeWeight($zipCode, $idCountry)) {
            return false;
        }

        if (!$to) {
            // Since we've already checked FROM condition, if TO is not provided - zip code is in range.
            return true;
        }

        // If zip code is bigger or equal to TO range - it's not in range.
        if (self::getZipCodeWeight($zipCode, $idCountry) > self::getZipCodeWeight($to, $idCountry)) {
            return false;
        }

        // Zip code is in range
        return true;
    }

    /**
     * Checks if $from and $to zip codes can form a valid range
     *
     * @param $from
     * @param $to
     * @param $idCountry
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function isValidRange(
        $from,
        $to,
        $idCountry
    ) {
        if (!self::isZipCode($from, $idCountry, false) ||
            !self::isZipCode($to, $idCountry, true)
        ) {
            return false;
        }

        if ($to && self::getZipCodeWeight($from, $idCountry) > self::getZipCodeWeight($to, $idCountry)) {
            return false;
        }

        return true;
    }

    /**
     * Checks if given string is a valid zip code
     *
     * @param $zipCode
     * @param $idCountry
     * @param bool $canBeEmpty
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function isZipCode($zipCode, $idCountry, $canBeEmpty = false)
    {
        $country = new Country($idCountry);
        if (!$canBeEmpty && self::isEmpty($zipCode)) {
            return false;
        }

        $zipCode = self::normalize($zipCode);

        if (0 === strpos($zipCode, '-')) {
            return false;
        }

        if ((Tools::strlen($zipCode) - 1) === Tools::strrpos($zipCode, '-')) {
            return false;
        }
        // validates zip codes for specific country
        if ($country->zip_code_format && !$country->checkZipCode($zipCode)) {
            return false;
        } elseif (!Validate::isPostCode($zipCode)) {
            return false;
        }

        return !preg_match('/[^0-9a-z- ]/i', $zipCode);
    }

    /**
     * Gets integer weight of zip code, can be used in zip codes comparison
     *
     * @param $zipCode
     *
     * @return int
     */
    public static function getZipCodeWeight($zipCode, $idCountry)
    {
        $addressAdapter = new AddressAdapter();
        $zipCode = $addressAdapter->getZipCodeByCountry($idCountry, $zipCode);
        $zipCode = self::normalize($zipCode);
        $strLen = Tools::strlen($zipCode);
        $weightSum = 0;

        for ($i = 0; $i < $strLen; $i++) {
            $weightSum += ord($zipCode[$i]) * pow(10, ($strLen - $i - 1) * 2);
        }

        return $weightSum;
    }

    public static function getNumericZipCode($zipCode, $idCountry)
    {
        $addressAdapter = new AddressAdapter();
        $zipCode = $addressAdapter->getZipCodeByCountry($idCountry, $zipCode);

        return self::normalize($zipCode);
    }

    /**
     * Normalize zip code - cast to string and remove spaces
     *
     * @param string|int $zipCode
     *
     * @return string
     */
    public static function normalize($zipCode)
    {
        return str_replace(' ', '', (string) $zipCode);
    }

    /**
     * Checks if zip code is empty
     *
     * @param $zipCode
     *
     * @return bool
     */
    public static function isEmpty($zipCode)
    {
        $zipCode = self::normalize($zipCode);

        return 0 === Tools::strlen($zipCode);
    }


    /**
     * @param ZoneRangeObjectCollection $zoneRanges
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function validateAndFormatZoneRanges(ZoneRangeObjectCollection $zoneRanges)
    {
        if (empty($zoneRanges)) {
            $this->errors[] = $this->module->l('Zone ranges cannot be empty');

            return false;
        }

        foreach ($zoneRanges as $zoneRange) {
            if (!$this->checkIfEmptyRange($zoneRange)) {
                $this->errors[] = $this->module->l('Zone ranges cannot have empty values');

                return false;
            }

            if (!$this->adaptAndValidateZipCode($zoneRange)) {
                $country = new Country($zoneRange->getIdCountry());
                $this->errors[] = sprintf(
                    $this->module->l('%s (%s) is not a valid zone range. Zip code input format is %s.'),
                    $country->name[$this->language->id],
                    $this->returnZipString($zoneRange),
                    $country->zip_code_format
                );

                return false;
            }
        }

        return true;
    }

    /**
     * @param ZoneRangeObject $zoneRange
     * @return bool
     */
    public function checkIfEmptyRange($zoneRange)
    {
        $includeAllRanges = (bool) $zoneRange->getIncludeAll();

        if (!$includeAllRanges &&
            (!$zoneRange->getZipCodeFrom() ||
                !$zoneRange->getZipCodeTo())
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param ZoneRangeObject $zoneRange
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function adaptAndValidateZipCode(ZoneRangeObject $zoneRange)
    {
        if (!ZoneRangeValidate::isValidRange(
            $zoneRange->getZipCodeFrom(),
            $zoneRange->getZipCodeTo(),
            $zoneRange->getIdCountry()
        ) && !$zoneRange->getIncludeAll()
        ) {
            $country = new Country($zoneRange->getIdCountry());
            $zipCodeAdapter = new ZipCodeAdapter($country, $zoneRange->getZipCodeFrom());
            $zoneRange->setZipCodeFrom($zipCodeAdapter->getWithCountryCode());

            $zipCodeAdapter = new ZipCodeAdapter($country, $zoneRange->getZipCodeTo());
            $zoneRange->setZipCodeTo($zipCodeAdapter->getWithCountryCode());

            if (!ZoneRangeValidate::isValidRange(
                $zoneRange->getZipCodeFrom(),
                $zoneRange->getZipCodeTo(),
                $zoneRange->getIdCountry()
            ) && !$zoneRange->getIncludeAll()
            ) {
                return false;
            }
        }

        return true;
    }

    private function returnZipString(ZoneRangeObject $zoneRange)
    {
        return $zoneRange->getIncludeAll() ?
            $this->module->l('All ranges') :
            sprintf('%s - %s', $zoneRange->getZipCodeFrom(), $zoneRange->getZipCodeTo());
    }
}
