<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */


namespace Invertus\dpdBaltics\Provider;

use DPDBaltics;
use Invertus\dpdBaltics\Config\Config;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ImportExportOptionsProvider
{

    /**
     * @var DPDBaltics
     */
    private $module;

    public function __construct(DPDBaltics $module)
    {
        $this->module = $module;
    }

    /**
     * Get all available export options
     *
     * @param bool $withZip  Whether to include zip otpion or not
     *
     * @return array
     */
    public function getImportExportOptions()
    {
        $options = [
            [
                'id' => Config::IMPORT_EXPORT_OPTION_ZONES,
                'name' => $this->module->l('Zones'),
            ],
            [
                'id' => Config::IMPORT_EXPORT_OPTION_SETTINGS,
                'name' => $this->module->l('Settings'),
            ],
            [
                'id' => Config::IMPORT_EXPORT_OPTION_PRODUCTS,
                'name' => $this->module->l('Products'),
            ],
            [
                'id' => Config::IMPORT_EXPORT_OPTION_PRICE_RULES,
                'name' => $this->module->l('Price rules'),
            ],
            [
                'id' => Config::IMPORT_EXPORT_OPTION_ADDRESS_TEMPLATES,
                'name' => $this->module->l('Address templates'),
            ],
            [
                'id' => Config::IMPORT_EXPORT_OPTION_ALL_ZIP,
                'name' => $this->module->l('All in ZIP'),
            ],
        ];


        return $options;
    }
}
