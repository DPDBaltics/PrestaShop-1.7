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

class DPDLog extends ObjectModel
{

    /**
     * @var string
     */
    public $request;

    /**
     * @var string
     */
    public $response;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $date_add;

    /**
     * @var array
     */
    public static $definition = [
        'table' => 'dpd_log',
        'primary' => 'id_dpd_log',
        'fields' => [
            'request' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'response' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'status' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
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
     * @throws PrestaShopException
     */
    public function delete()
    {
        $parcelProducts = new PrestaShopCollection('DPDParcelProduct');
        $parcelProducts->where('id_dpd_parcel', '=', $this->id);

        return parent::delete();
    }
}
