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

namespace Invertus\dpdBaltics\Plugin;

interface PluginInterface
{
    public function getJs();

    public function getCss();

    public function getJsVars();

    /**
     * Sets a JS variable
     * @param string $name variable's name
     * @param string $value variable's value
     */
    public function setJsVar($name, $value);
}
