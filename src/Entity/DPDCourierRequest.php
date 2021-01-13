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

class DPDCourierRequest extends ObjectModel
{

    public $sender_name;

    public $sender_phone_code;

    public $sender_phone;

    public $sender_id_ws_country;

    public $country;
    
    public $sender_postal_code;
    
    public $sender_city;
    
    public $sender_address;
    
    public $sender_additional_information;
    
    public $order_nr;
    
    public $pick_up_time;
    
    public $sender_work_until;
    
    public $weight;
    
    public $parcels_count;
    
    public $pallets_count;
    
    public $comment_for_courier;
    
    /**
     * Additional services are saved in encoded JSON object, in __construct() we decode it to array of ids
     *
     * @var array|int[]
     */
    public $date_add;

    public $date_upd;

    public static $definition = [
        'table' => 'dpd_courier_request',
        'primary' => 'id_dpd_courier_request',
        'fields' => [
            'sender_name' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'sender_phone_code' => ['type' => self::TYPE_STRING, 'required' => 1, 'validate' => 'isString'],
            'sender_phone' => ['type' => self::TYPE_STRING, 'required' => 1, 'validate' => 'isString'],
            'sender_id_ws_country' => ['type' => self::TYPE_INT, 'required' => 1, 'validate' => 'isInt'],
            'sender_postal_code' => ['type' => self::TYPE_STRING, 'required' => 1, 'validate' => 'isString'],
            'sender_city' => ['type' => self::TYPE_STRING, 'required' => 1, 'validate' => 'isString'],
            'sender_address' => ['type' => self::TYPE_STRING, 'required' => 1, 'validate' => 'isString'],
            'sender_additional_information' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'order_nr' => ['type' => self::TYPE_STRING, 'required' => 1, 'validate' => 'isString'],
            'pick_up_time' => ['type' => self::TYPE_STRING, 'required' => 1, 'validate' => 'isString'],
            'sender_work_until' => ['type' => self::TYPE_STRING, 'required' => 1, 'validate' => 'isString'],
            'weight' => ['type' => self::TYPE_FLOAT, 'required' => 1, 'validate' => 'isFloat'],
            'parcels_count' => ['type' => self::TYPE_INT, 'required' => 1, 'validate' => 'isInt'],
            'pallets_count' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'comment_for_courier' => ['type' => self::TYPE_STRING,  'validate' => 'isString'],
            'date_add' => ['type' => self::TYPE_DATE],
            'date_upd' => ['type' => self::TYPE_DATE],
        ],
    ];
}
