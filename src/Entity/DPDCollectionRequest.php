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

class DPDCollectionRequest extends ObjectModel
{
    const ADDRESS_TYPE_COMPANY = 'company';
    const ADDRESS_TYPE_INDIVIDUAL = 'individual';

    public $pickup_address_full_name;

    public $pickup_address_mobile_phone_code;

    public $pickup_address_mobile_phone;

    public $pickup_address_email;

    public $pickup_address_id_ws_country;

    public $pickup_address_zip_code;

    public $pickup_address_city;

    public $pickup_address_address;

    public $receiver_address_full_name;

    public $receiver_address_mobile_phone_code;

    public $receiver_address_mobile_phone;

    public $receiver_address_email;

    public $receiver_address_id_ws_country;

    public $receiver_address_zip_code;

    public $receiver_address_city;

    public $receiver_address_address;

    public $info1;

    public $info2;

    /**
     * Additional services are saved in encoded JSON object, in __construct() we decode it to array of ids
     *
     * @var array|int[]
     */
    public $date_add;

    public $date_upd;

    public static $definition = [
        'table' => 'dpd_collection_request',
        'primary' => 'id_dpd_collection_request',
        'fields' => [
            'pickup_address_full_name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName'],
            'pickup_address_mobile_phone_code' => ['type' => self::TYPE_STRING, 'required' => 1],
            'pickup_address_mobile_phone' => ['type' => self::TYPE_STRING, 'required' => 1],
            'pickup_address_email' => ['type' => self::TYPE_STRING, 'required' => 1, 'validate' => 'isEmail'],
            'pickup_address_id_ws_country' =>
                ['type' => self::TYPE_INT, 'required' => 1, 'validate' => 'isUnsignedInt'],
            'pickup_address_zip_code' => ['type' => self::TYPE_STRING, 'required' => 1],
            'pickup_address_city' => ['type' => self::TYPE_STRING, 'required' => 1, 'validate' => 'isGenericName'],
            'pickup_address_address' => ['type' => self::TYPE_STRING, 'required' => 1],
            'receiver_address_full_name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName'],
            'receiver_address_mobile_phone_code' => ['type' => self::TYPE_STRING, 'required' => 1],
            'receiver_address_mobile_phone' => ['type' => self::TYPE_STRING, 'required' => 1],
            'receiver_address_email' => ['type' => self::TYPE_STRING, 'required' => 1, 'validate' => 'isEmail'],
            'receiver_address_id_ws_country' =>
                ['type' => self::TYPE_INT, 'required' => 1, 'validate' => 'isUnsignedInt'],
            'receiver_address_zip_code' => ['type' => self::TYPE_STRING, 'required' => 1],
            'receiver_address_city' =>
                ['type' => self::TYPE_STRING, 'required' => 1, 'validate' => 'isGenericName'],
            'receiver_address_address' => ['type' => self::TYPE_STRING, 'required' => 1],

            'info1' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => 1],
            'info2' => ['type' => self::TYPE_STRING],
            'date_add' => ['type' => self::TYPE_DATE],
            'date_upd' => ['type' => self::TYPE_DATE],
        ],
    ];
}
