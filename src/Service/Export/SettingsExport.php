<?php

namespace Invertus\dpdBaltics\Service\Export;

use Configuration;
use DPDBaltics;
use Invertus\dpdBaltics\Config\Config;

/**
 * Class DPDSettingsExport is responsible returning settings data to be exported
 */
class SettingsExport implements ExportableInterface
{
    const FILE_NAME = 'SettingsExport';

    /**
     * @var int Number of exported fields
     */
    const EXPORT_COLUMNS_COUNT = 9;

    /**
     * @var DPDBaltics
     */
    private $module;

    public function __construct(DPDBaltics $module)
    {
        $this->module = $module;
    }

    /**
     * {@inheritdoc}
     */
    public function getRows()
    {
        $row = [
            (int) Configuration::get(Config::SHIPMENT_TEST_MODE),
            (int) Configuration::get(Config::PARCEL_TRACKING),
            (int) Configuration::get(Config::PARCEL_RETURN),
            (int) Configuration::get(Config::DPD_SHIPMENT_RETURN_CONFIRMATION),
            (int) Configuration::get(Config::PICKUP_MAP),
            Configuration::get(Config::PARCEL_DISTRIBUTION),
            Configuration::get(Config::AUTO_VALUE_FOR_REF),
            Configuration::get(Config::LABEL_PRINT_OPTION),
            Configuration::get(Config::DEFAULT_LABEL_FORMAT),
        ];

        // since it's only one row, we have to make sure we return array of rows
        return [$row];
    }

    /**
     * {@inheritdoc}
     */
    public function getFileName()
    {
        return sprintf(Config::IMPORT_EXPORT_OPTION_SETTINGS.'_%s.csv', date('Y-m-d_His'));
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return [
            $this->module->l('Shipment test mode', self::FILE_NAME),
            $this->module->l('Parcel tracking', self::FILE_NAME),
            $this->module->l('Parcel return', self::FILE_NAME),
            $this->module->l('Pickup map', self::FILE_NAME),
            $this->module->l('Parcel distribution', self::FILE_NAME),
            $this->module->l('Label print option', self::FILE_NAME),
            $this->module->l('Default label format', self::FILE_NAME),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function hasErrors()
    {
        return false;
    }
}
