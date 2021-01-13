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

class DPDShop extends ObjectModel
{
    /**
     * @var string
     */
    public $parcel_shop_id;

    /**
     * @var string
     */
    public $company;

    /**
     * @var string
     */
    public $country;

    /**
     * @var string
     */
    public $city;

    /**
     * @var string
     */
    public $p_code;

    /**
     * @var string
     */
    public $street;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $phone;

    /**
     * @var string
     */
    public $longitude;

    /**
     * @var string
     */
    public $latitude;

    /**
     * @var array
     */
    public static $definition = [
        'table' => 'dpd_shop',
        'primary' => 'id_dpd_shop',
        'fields' => [
            'parcel_shop_id' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'company' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'country' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'city' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'p_code' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'street' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'email' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'phone' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'longitude' => ['type' => self::TYPE_FLOAT, 'validate' => 'isFloat'],
            'latitude' => ['type' => self::TYPE_FLOAT, 'validate' => 'isFloat'],
        ],
    ];

    public static function getShopByPudoId($pudoId)
    {
        $pudoShops = new PrestaShopCollection('DPDShop');
        $pudoShops->where('parcel_shop_id', '=', pSQL($pudoId));

        return $pudoShops->getFirst();
    }
}
