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

class DPDOrderDeliveryTime extends ObjectModel
{
    /**
     * @var int
     */
    public $id_dpd_order_delivery_time;

    /**
     * @var int
     */
    public $id_cart;

    /**
     * @var string
     */
    public $delivery_time;

    public static $definition = [
        'table' => 'dpd_order_delivery_time',
        'primary' => 'id_dpd_order_delivery_time',
        'fields' => [
            'id_cart' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'delivery_time' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
        ],
    ];

    public static function getRepositoryClassName()
    {
        return 'DPDOrderDeliveryTimeRepository';
    }
}
