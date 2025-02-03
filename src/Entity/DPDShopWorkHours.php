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

if (!defined('_PS_VERSION_')) {
    exit;
}

class DPDShopWorkHours extends ObjectModel
{
    /**
     * @var string
     */
    public $parcel_shop_id;

    /**
     * @var string
     */
    public $week_day;

    /**
     * @var string
     */
    public $open_morning;

    /**
     * @var string
     */
    public $close_morning;

    /**
     * @var string
     */
    public $open_afternoon;

    /**
     * @var string
     */
    public $close_afternoon;

    /**
     * @var array
     */
    public static $definition = [
        'table' => 'dpd_shop_work_hours',
        'primary' => 'id_dpd_shop_work_hours',
        'fields' => [
            'parcel_shop_id' => ['type' => self::TYPE_STRING, 'required' => 1, 'validate' => 'isString'],
            'week_day' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'open_morning' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'close_morning' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'open_afternoon' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'close_afternoon' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
        ],
    ];
}
