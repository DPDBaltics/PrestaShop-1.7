<?php

namespace Invertus\dpdBaltics\Service\Zone;

use DPDZone;
use DPDZoneRange;
use Exception;
use Invertus\dpdBaltics\DTO\ZoneRangeObject;
use Invertus\dpdBaltics\Exception\ZoneUpdateException;
use Validate;

class UpdateZoneService
{
    /**
     * Updates DPD Zone and zone range
     *
     * @param $zoneId
     * @param $zoneName
     * @param $zoneRanges
     *
     * @return int
     * @throws ZoneUpdateException
     */
    public function updateZone($zoneId, $zoneName, $zoneRanges)
    {
        try {
            $zone = new DPDZone($zoneId);
            $zone->deleteZoneRanges();

            if (!Validate::isUnsignedId($zoneId) || !Validate::isLoadedObject($zone)) {
                $zone->name = $zoneName;
                $zone->is_custom = true;
                $zone->save();
            } elseif ($zone->name != $zoneName) {
                $zone->name = $zoneName;
                $zone->update();
            }

            /**
             *  @var ZoneRangeObject $range
             */
            foreach ($zoneRanges as $range) {
                $zoneRange = new DPDZoneRange();
                $zoneRange->id_dpd_zone = $zone->id;
                $zoneRange->include_all_zip_codes = (bool) $range->getIncludeAll();
                $zoneRange->id_country = (int) $range->getIdCountry();
                $zoneRange->zip_code_from = $range->getZipCodeFrom();
                $zoneRange->zip_code_to =  $range->getZipCodeTo();
                $zoneRange->zip_code_from_numeric = $range->getNumericZipCodeFrom();
                $zoneRange->zip_code_to_numeric = $range->getNumericZipCodeTo();

                $zoneRange->save();
            }
        } catch (Exception $e) {
            throw new ZoneUpdateException($e->getMessage());
        };

        return $zone->id;
    }
}
