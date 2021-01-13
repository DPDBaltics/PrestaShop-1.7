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

class DPDShipmentError extends ObjectModel
{
    public $id_dpd_shipment_error;

    public $id_shipment;

    public $error;

    public static $definition = array(
        'table' => 'dpd_shipment_error',
        'primary' => 'id_dpd_shipment_error',
        'fields' => array(
            'id_shipment' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'error' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
        ),
    );
}
