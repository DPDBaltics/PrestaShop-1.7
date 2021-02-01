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

class DPDAddressTemplate extends ObjectModel
{
    const ADDRESS_TYPE_COLLECTION_REQUEST = 'collection_request';
    const ADDRESS_TYPE_RETURN_SERVICE = 'return_service';

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;


    /**
     * @var string
     */
    public $full_name;

    /**
     * @var string
     */
    public $mobile_phone;

    /**
     * @var string
     */
    public $mobile_phone_code;

    /**
     * @var int
     */
    public $dpd_country_id;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $zip_code;

    /**
     * @var string
     */
    public $dpd_city_name;

    /**
     * @var string
     */
    public $address;

    /**
     * @var string
     */
    public $date_add;

    /**
     * @var string
     */
    public $date_upd;

    /**
     * @var array
     */
    public static $definition = [
        'table' => 'dpd_address_template',
        'primary' => 'id_dpd_address_template',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'required' => 1, 'validate' => 'isGenericName', 'size' => 40],
            'type' => ['type' => self::TYPE_STRING],
            'full_name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 40],
            'mobile_phone' => ['type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'size' => 50],
            'mobile_phone_code' => ['type' => self::TYPE_STRING, 'size' => 5],
            'email' => ['type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 100],
            'dpd_country_id' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'zip_code' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 9],
            'dpd_city_name' => ['type' => self::TYPE_STRING, 'size' => 40],
            'address' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 40],
            'date_add' => ['type' => self::TYPE_DATE],
            'date_upd' => ['type' => self::TYPE_DATE],
        ],
    ];
}
