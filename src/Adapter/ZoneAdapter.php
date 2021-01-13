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
use Invertus\dpdBaltics\Collection\ZoneRangeObjectCollection;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\DTO\ZoneRangeObject;

class ZoneAdapter
{
    /**
     * @param array $zoneRanges
     * @return ZoneRangeObjectCollection
     */
    public function convertZoneRangesToObjects(array $zoneRanges)
    {
        $zoneRangesCollection = new ZoneRangeObjectCollection();

        foreach ($zoneRanges as $zoneRange) {
            $returnZoneRange = new ZoneRangeObject();
            $returnZoneRange->setIdCountry($zoneRange['countryId']);
            $returnZoneRange->setZipCodeFrom($zoneRange['zipFrom']);
            $returnZoneRange->setZipCodeTo($zoneRange['zipTo']);
            $returnZoneRange->setIncludeAll($zoneRange['allRanges']);
            $returnZoneRange->setCountryName($zoneRange['countryName']);
            $zoneRangesCollection->add($returnZoneRange);
        }

        return $zoneRangesCollection;
    }

    /**
     * @param array $zoneRanges
     * @return array
     */
    public function convertSnakeToCamel(array $zoneRanges)
    {
        $return = [];

        foreach ($zoneRanges as $zoneRange) {
            $returnZoneRange = [];
            $returnZoneRange['countryId'] = $zoneRange['id_country'];
            $returnZoneRange['zipFrom'] = $zoneRange['zip_from'];
            $returnZoneRange['zipTo'] = $zoneRange['zip_to'];
            $returnZoneRange['allRanges'] = $zoneRange['all_ranges'];
            $returnZoneRange['countryName'] = '';
            $return[] = $returnZoneRange;
        }

        return $return;
    }

    /**
     * @param array $zoneRanges
     * @return array
     */
    public function convertToFormat(array $zoneRanges)
    {
        $return = [];

        $ltCountyId = Country::getByIso(Config::LITHUANIA_ISO_CODE);
        $ltCountry = new Country($ltCountyId);
        $ltCountryZipCodeFormatLength = strlen($ltCountry->zip_code_format);
        foreach ($zoneRanges as $zoneRange) {
            if ($ltCountry->id !== $zoneRange['countryId']) {
                $return[] = $zoneRange;
                continue;
            }
            if ($ltCountryZipCodeFormatLength !== strlen($zoneRange['zipFrom'])) {
                $zoneRange['zipFrom'] = str_pad($zoneRange['zipFrom'], $ltCountryZipCodeFormatLength, "0", STR_PAD_LEFT);
            }
            if ($ltCountryZipCodeFormatLength !== strlen($zoneRange['zipTo'])) {
                $zoneRange['zipTo'] = str_pad($zoneRange['zipTo'], $ltCountryZipCodeFormatLength, "0", STR_PAD_LEFT);
            }
            $return[] = $zoneRange;
        }

        return $return;
    }
}
