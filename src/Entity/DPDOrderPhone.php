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

class DPDOrderPhone extends ObjectModel
{
    /**
     * @var int
     */
    public $id_dpd_order_phone;

    /**
     * @var int
     */
    public $id_cart;

    /**
     * @var string
     */
    public $phone;

    /**
     * @var string
     */
    public $phone_area;

    public static $definition = [
        'table' => 'dpd_order_phone',
        'primary' => 'id_dpd_order_phone',
        'fields' => [
            'id_cart' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'phone' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'phone_area' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
        ],
    ];

    public static function getRepositoryClassName()
    {
        return 'DPDOrderRepository';
    }
}
