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
