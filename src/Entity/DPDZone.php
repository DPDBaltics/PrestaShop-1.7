<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 *  International Registered Trademark & Property of INVERTUS, UAB
 */

use Invertus\dpdBaltics\Repository\ZoneRangeRepository;
use Invertus\dpdBaltics\Validate\Zone\ZoneRangeValidate;

/**
 * Class DPDZone
 */
class DPDZone extends ObjectModel
{
    const MIN_NAME_LENGTH = 3;

    /**
     * @var string
     */
    public $name;


    /**
     * @var bool TRUE if zone is created by merchant, FALSE otherwise
     */
    public $is_custom;

    /**
     * @var array
     */
    public static $definition = [
        'table' => 'dpd_zone',
        'primary' => 'id_dpd_zone',
        'fields' => [
            'name' => [
                'type' => self::TYPE_STRING,
                'required' => 1,
                'validate' => 'isGenericName',
                'size' => 255
            ],
            'is_custom' => ['type' => self::TYPE_BOOL, 'required' => 1, 'validate' => 'isBool'],
        ],
    ];

    /**
     * Check if address falls into given zones
     *
     * @param Address $address
     * @param array $zones
     *
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function checkAddressInZones(Address $address, array $zones)
    {
        $country = new Country($address->id_country);

        /** @var DPDBaltics $module */
        $module = Module::getInstanceByName('dpdbaltics');

        /** @var ZoneRangeRepository $zoneRangeRepo */
        $zoneRangeRepo = $module->getModuleContainer()->get(ZoneRangeRepository::class);

        foreach ($zones as $zone) {
            // Get ranges by zone and country
            $ranges = $zoneRangeRepo->findBy([
                'id_dpd_zone' => $zone['id'],
                // Check by country as well, because the zone must match the country of address
                'id_country' => $country->id,
            ]);

            foreach ($ranges as $range) {
                if ($range['include_all_zip_codes']) {
                    return true;
                }

                if (ZoneRangeValidate::isZipCodeInRange(
                    $address->postcode,
                    $range['zip_code_from'],
                    $range['zip_code_to'],
                    $country->id
                )
                ) {
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * Delete all zone ranges
     *
     * @return bool
     *
     * @throws PrestaShopException
     */
    public function deleteZoneRanges()
    {
        if (!$this->id) {
            return true;
        }

        $collection = new Collection('DPDZoneRange');
        $collection->where('id_dpd_zone', '=', $this->id);

        /** @var DPDZoneRange $zoneRange */
        foreach ($collection as $zoneRange) {
            if (!$zoneRange->delete()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get zone ranges
     *
     * @return Collection|array
     * @throws PrestaShopException
     */
    public function getRanges()
    {
        if (!$this->id) {
            return [];
        }

        $collection = new Collection('DPDZoneRange');
        $collection->where('id_dpd_zone', '=', $this->id);

        return $collection->getResults();
    }
}
