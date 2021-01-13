<?php

namespace Invertus\dpdBaltics\Provider;

use DPDBaltics;
use Invertus\dpdBaltics\Config\Config;

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
