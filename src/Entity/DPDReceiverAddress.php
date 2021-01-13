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

class DPDReceiverAddress extends ObjectModel
{
    /**
     * @var int
     */
    public $id_order;

    /**
     * @var int
     */
    public $id_origin_address;

    /**
     * @var array
     */
    public static $definition = [
        'table' => 'dpd_receiver_address',
        'primary' => 'id_dpd_receiver_address',
        'fields' => [
            'id_order' => ['type' => self::TYPE_INT, 'required' => 1, 'validate' => 'isInt'],
            'id_origin_address' => ['type' => self::TYPE_INT, 'required' => 1, 'validate' => 'isInt'],
        ],
    ];

    public static function getRepositoryClassName()
    {
        return 'DPDReceiverAddressRepository';
    }
}
