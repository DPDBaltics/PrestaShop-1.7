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

namespace  Invertus\dpdBaltics\Plugin\SearchBox;

use Module;
use Smarty;

class DPDSearchBoxPluginFactory
{
    /**
     * Creates a search box plugin
     *
     * @param Module $module
     * @param Smarty $smarty
     * @param string $pluginName
     *
     * @return Chosen|null
     */
    public static function create(Module $module, Smarty $smarty, $pluginName = 'chosen')
    {
        $plugin = null;

        switch ($pluginName) {
            case 'chosen':
                $plugin = new Chosen($module, $smarty);
                break;
        }

        return $plugin;
    }
}
