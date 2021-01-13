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

namespace Invertus\dpdBaltics\Helper;

use Configuration;
use DPDShipment;
use Invertus\dpdBaltics\Config\Config;

class ShipmentHelper
{
    public function getReference($orderId, $orderReference)
    {
        if (Configuration::get(Config::AUTO_VALUE_FOR_REF) == DPDShipment::AUTO_VAL_REF_NONE) {
            return '';
        }
        if (Configuration::get(Config::AUTO_VALUE_FOR_REF) == DPDShipment::AUTO_VAL_REF_ORDER_REF) {
            return $orderReference;
        }
        if (Configuration::get(Config::AUTO_VALUE_FOR_REF) == DPDShipment::AUTO_VAL_REF_ORDER_ID) {
            return $orderId;
        }
    }
}
