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

class DPDPudo extends ObjectModel
{
    /** @var int */
    public $id_cart;

    /** @var int */
    public $id_carrier;

    /** @var string */
    public $country_code;

    /** @var string */
    public $city;

    /** @var string */
    public $street;

    /** @var string */
    public $post_code;

    /** @var string */
    public $pudo_id;

    public static $definition = [
        'table' => 'dpd_pudo_cart',
        'primary' => 'id',
        'fields' => [
            'id_cart' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'id_carrier' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'country_code' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 2],
            'city' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'street' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'post_code' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 10],
            'pudo_id' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true],
        ]
    ];

    /**
     * Get repository class name
     *
     * @return string
     */
    public static function getRepositoryClassName()
    {
        return 'DPDPudoRepository';
    }
}
