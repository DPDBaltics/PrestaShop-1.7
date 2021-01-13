<?php


class DPDShipment extends ObjectModel
{
    const MAX_PARCELS_COUNT = 10;
    const MAX_REFERENCE_LENGTH = 30;

    const AUTO_VAL_REF_NONE = 'none';
    const AUTO_VAL_REF_ORDER_ID = 'order_id';
    const AUTO_VAL_REF_ORDER_REF = 'order_reference';
    /**
     * string - identifies shipment type for close manifest
     */
    const SHIPMENT_TYPE = 'REGULAR_SHIPMENT_TYPE';

    /**
     * @var int
     */
    public $id_order;

    /**
     * @var bool
     */
    public $printed_label;

    /**
     * @var bool
     */
    public $printed_manifest;

    /**
     * @var bool
     */
    public $manifest_closed;

    /**
     * @var string
     */
    public $date_print;

    /**
     * @var string
     */
    public $label_format;

    /**
     * @var int
     */
    public $label_position;

    /**
     * @var string
     */
    public $reference1;

    /**
     * @var string
     */
    public $reference2;

    /**
     * @var string
     */
    public $reference3;

    /**
     * @var string
     */
    public $reference4;

    /**
     * @var string
     */
    public $date_add;

    /**
     * @var string
     */
    public $date_upd;

    /**
     * @var string
     */
    public $date_shipment;

    /**
     * @var int
     */
    public $id_contract;

    /**
     * @var int
     */
    public $id_service;

    public $id_unique_service;

    /** @var  string */
    public $id_pudo;

    public $pudo_country_code;

    /**
     * @var int
     */
    public $id_ws_manifest;

    /**
     * @var string
     */
    public $pudo_address;

    /** @var string */
    public $id_sender_address;

    /**
     * @var bool
     */
    public $saved;

    /**
     * @var String
     */
    public $pl_number;

    /**
     * @var String
     */
    public $return_pl_number;

    /**
     * @var String
     */
    public $document_return_number;

    /**
     * @var String
     */
    public $is_document_return_enabled;

    /**
     * @var bool
     */
    public $is_test;

    /**
     * @var int
     */
    public $num_of_parcels;

    /**
     * @var float
     */
    public $weight;

    /**
     * @var float
     */
    public $goods_price;

    /**
     * @var array
     */
    public static $definition = [
        'table' => 'dpd_shipment',
        'primary' => 'id_dpd_shipment',
        'fields' => [
            'id_order' => ['type' => self::TYPE_INT, 'required' => 1, 'validate' => 'isUnsignedInt'],
            'id_contract' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_service' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_unique_service' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_pudo' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'],
            'id_sender_address' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'pl_number' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'return_pl_number' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'document_return_number' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'is_document_return_enabled' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'pudo_country_code' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'],
            'printed_label' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'printed_manifest' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'label_format' => ['type' => self::TYPE_STRING],
            'label_position' => ['type' => self::TYPE_STRING],
            'reference1' => ['type' => self::TYPE_STRING],
            'reference2' => ['type' => self::TYPE_STRING],
            'reference3' => ['type' => self::TYPE_STRING],
            'reference4' => ['type' => self::TYPE_STRING],
            'date_print' => ['type' => self::TYPE_DATE],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_shipment' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'manifest_closed' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'id_ws_manifest' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'pudo_address' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'],
            'saved' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'num_of_parcels' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'weight' => ['type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'],
            'goods_price' => ['type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'],
            'is_test' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
        ],
    ];
}
