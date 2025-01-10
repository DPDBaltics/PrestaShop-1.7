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

namespace Invertus\dpdBaltics\Adapter;

use Configuration;

if (!defined('_PS_VERSION_')) {
    exit;
}


class DPDConfigurationAdapter
{
    public function get($key)
    {
        return Configuration::get($key);
    }

    public function set($key, $value)
    {
        return Configuration::updateValue($key, $value);
    }

    public function getIdByName($key)
    {
        return Configuration::getIdByName($key);
    }
}
