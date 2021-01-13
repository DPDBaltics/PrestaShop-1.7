<?php

namespace Invertus\dpdBaltics\Service\Import;

use Configuration;
use DPDBaltics;
use DPDParcel;
use DPDShipment;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Service\Export\SettingsExport;
use Shop;

/**
 * Class DPDSettingsImport is responsible for importing module settings
 */
class SettingsImport implements ImportableInterface
{
    const FILE_NAME = 'SettingsImport';

    const SHIPMENT_TEST_MODE_COLUMN = 1;
    const PARCEL_TRACKING_COLUMN = 2;
    const PARCEL_RETURN_COLUMN = 3;
    const PICKUP_MAP_COLUMN = 4;
    const PARCEL_DISTRIBUTION_COLUMN = 5;
    const AUTO_VALUE_FOR_REF = 6;
    const LABEL_PRINT_OPTION_COLUMN = 7;
    const DEFAULT_LABEL_FORMAT_COLUMN = 8;
    const DEFAULT_LABEL_POSITION_COLUMN = 8;

    /**
     * @var array Errors that happended during import or data validation
     */
    private $errors = [];

    /**
     * @var DPDBaltics
     */
    private $module;

    public function __construct(DPDBaltics $module)
    {
        $this->module = $module;
    }

    /**
     * @var int
     */
    private $importedRowsCount = 0;

    /**
     * {@inheritdoc}
     */
    public function importRows(array $rows)
    {
        if (!$this->validate($rows)) {
            return;
        }

        $idShops = Shop::getContextListShopID();
        foreach ($rows as $row) {
            foreach ($idShops as $idShop) {
                for ($columnNum = 1; SettingsExport::EXPORT_COLUMNS_COUNT > $columnNum; $columnNum++) {
                    $importData = $this->parseColumnNameAndValue($row, $columnNum);
                    if (false === $importData) {
                        continue;
                    }

                    list($name, $value) = $importData;
                    Configuration::updateValue($name, $value, null, null, $idShop);
                    Configuration::updateValue($name, $value);
                }
            }
            $this->importedRowsCount++;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * {@inheritdoc}
     */
    public function getImportedRowsCount()
    {
        return $this->importedRowsCount;
    }

    /**
     * {@inheritdoc}
     */
    public function getWarnings()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmations()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function useTransaction()
    {
    }

    /**
     * Validate import rows
     *
     * @param array $rows
     *
     * @return bool
     */
    private function validate(array $rows)
    {
        $row = reset($rows);

        if (false === $row) {
            $this->errors[] = $this->module->l('There is no settings to import', self::FILE_NAME);
            return false;
        }

        if (SettingsExport::EXPORT_COLUMNS_COUNT < count($rows)) {
            $this->errors[] = $this->module->l('Cannot import settings due to missing configuration data', self::FILE_NAME);
            return false;
        }

        return true;
    }

    /**
     * Get column name and value for import
     *
     * @param array $row Single row data
     * @param int $column Number of column in the row
     *
     * @return array|false  Array of name and value or FALSE if index column is out of range
     */
    private function parseColumnNameAndValue(array $row, $column)
    {
        $defaultConfig = Config::getDefaultConfiguration();
        $index = $column - 1;

        if (self::SHIPMENT_TEST_MODE_COLUMN === $column) {
            $name = Config::SHIPMENT_TEST_MODE;
            $value = (int)$row[$index];
        } elseif (self::PARCEL_TRACKING_COLUMN === $column) {
            $name = Config::PARCEL_TRACKING;
            $value = (int)$row[$index];
        } elseif (self::PARCEL_RETURN_COLUMN === $column) {
            $name = Config::PARCEL_RETURN;
            $value = (int)$row[$index];
        } elseif (self::PICKUP_MAP_COLUMN === $column) {
            $name = Config::PICKUP_MAP;
            $value = (int)$row[$index];
        } elseif (self::PARCEL_DISTRIBUTION_COLUMN === $column) {
            $name = Config::PARCEL_DISTRIBUTION;
            $value = $row[$index];

            if (!in_array(
                $value,
                [
                    DPDParcel::DISTRIBUTION_NONE,
                    DPDParcel::DISTRIBUTION_PARCEL_PRODUCT,
                    DPDParcel::DISTRIBUTION_PARCEL_QUANTITY
                ],
                false
            )) {
                $value = $defaultConfig[Config::PARCEL_DISTRIBUTION];
            }
        } elseif (self::AUTO_VALUE_FOR_REF === $column) {
            $name = Config::AUTO_VALUE_FOR_REF;
            $value = $row[$index];

            if (!in_array(
                $value,
                [
                    DPDShipment::AUTO_VAL_REF_NONE,
                    DPDSHipment::AUTO_VAL_REF_ORDER_ID,
                    DPDShipment::AUTO_VAL_REF_ORDER_REF
                ],
                false
            )) {
                $value = $defaultConfig[Config::AUTO_VALUE_FOR_REF];
            }
        } elseif (self::LABEL_PRINT_OPTION_COLUMN === $column) {
            $name = Config::LABEL_PRINT_OPTION;
            $value = $row[$index];

            if (!in_array(
                $value,
                [
                    Config::PRINT_OPTION_BROWSER,
                    Config::PRINT_OPTION_DOWNLOAD,
                ],
                false
            )) {
                $value = $defaultConfig[Config::LABEL_PRINT_OPTION];
            }
        } elseif (self::DEFAULT_LABEL_FORMAT_COLUMN === $column) {
            $name = Config::DEFAULT_LABEL_FORMAT;
            $value = $row[$index];
        } elseif (self::DEFAULT_LABEL_POSITION_COLUMN === $column) {
            $name = Config::DEFAULT_LABEL_POSITION;
            $value = $row[$index];
        } else {
            return false;
        }

        return [$name, $value];
    }

    /**
     * Delete previous data if it is relevant
     *
     */
    public function deleteOldData()
    {
    }
}
