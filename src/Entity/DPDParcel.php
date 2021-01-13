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

class DPDParcel extends ObjectModel
{
    const DISTRIBUTION_NONE = 'none';
    const DISTRIBUTION_PARCEL_PRODUCT = 'parcel_product';
    const DISTRIBUTION_PARCEL_QUANTITY = 'parcel_quantity';

    const MAX_REFERENCE_LENGTH = 50;

    const SHIPMENT_TYPE_PACKAGE = 'package';

    /**
     * @var int
     */
    public $id_dpd_shipment;

    /**
     * @var string
     */
    public $shipment_type;

    /**
     * @var bool
     */
    public $is_master;

    /**
     * @var float
     */
    public $weight;

    /**
     * @var float
     */
    public $length;

    /**
     * @var float
     */
    public $height;

    /**
     * @var float
     */
    public $width;

    /**
     * @var string
     */
    public $volume;

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
     * @var int
     */
    public $id_ws_parcel;

    /**
     * @var int
     */
    public $price;

    /**
     * @var string
     */
    public $goodsCurrency;

    /**
     * @var int
     */
    public $codPaymentType;

    /**
     * @var array
     */
    public static $definition = [
        'table' => 'dpd_parcel',
        'primary' => 'id_dpd_parcel',
        'fields' => [
            'id_dpd_shipment' => ['type' => self::TYPE_INT, 'required' => 1, 'validate' => 'isUnsignedInt'],
            'id_ws_parcel' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'price' => ['type' => self::TYPE_FLOAT],
            'shipment_type' => ['type' => self::TYPE_STRING],
            'is_master' => ['type' => self::TYPE_STRING],
            'weight' => ['type' => self::TYPE_FLOAT, 'validate' => 'isFloat'],
            'length' => ['type' => self::TYPE_FLOAT],
            'height' => ['type' => self::TYPE_FLOAT],
            'width' => ['type' => self::TYPE_FLOAT],
            'volume' => ['type' => self::TYPE_STRING],
            'reference1' => ['type' => self::TYPE_STRING],
            'reference2' => ['type' => self::TYPE_STRING],
            'reference3' => ['type' => self::TYPE_STRING],
            'reference4' => ['type' => self::TYPE_STRING],
        ],
    ];

    /**
     * @return string
     */
    public static function getRepositoryClassName()
    {
        return 'DPDParcelRepository';
    }

    /**
     * Delete parcel with it's products
     *
     * @return bool
     */
    public function delete()
    {
        $parcelProducts = new PrestaShopCollection('DPDParcelProduct');
        $parcelProducts->where('id_dpd_parcel', '=', $this->id);

        return parent::delete();
    }
}
