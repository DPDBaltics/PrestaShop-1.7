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

namespace Invertus\dpdBaltics\Infrastructure\Adapter;

use Module;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ModuleFactory
{
    /**
     * @return \DPDBaltics|false|Module|null
     */
    public function getModule()
    {
        return Module::getInstanceByName('dpdbaltics') ?: null;
    }
}