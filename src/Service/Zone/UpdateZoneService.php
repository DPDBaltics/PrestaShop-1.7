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


namespace Invertus\dpdBaltics\Service\Zone;

use DPDZone;
use DPDZoneRange;
use Exception;
use Invertus\dpdBaltics\DTO\ZoneRangeObject;
use Invertus\dpdBaltics\Exception\ZoneUpdateException;
use Validate;

if (!defined('_PS_VERSION_')) {
    exit;
}

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
