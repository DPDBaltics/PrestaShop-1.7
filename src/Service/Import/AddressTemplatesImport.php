<?php

namespace Invertus\dpdBaltics\Service\Import;

use Configuration;
use Db;
use DPDAddressTemplate;
use DPDBaltics;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Repository\ShopRepository;
use Shop;
use Validate;

class AddressTemplatesImport implements ImportableInterface
{
    const FILE_NAME = 'AddressTemplatesImport';

    const POSITION_NAME = 0;
    const POSITION_TYPE = 1;
    const POSITION_FULL_NAME = 2;
    const POSITION_PHONE_CODE = 3;
    const POSITION_PHONE = 4;
    const POSITION_EMAIL = 5;
    const POSITION_COUNTRY_ID = 6;
    const POSITION_ZIP_CODE = 7;
    const POSITION_CITY_NAME = 8;
    const POSITION_ADDRESS = 9;
    const POSITION_ALL_SHOPS = 10;
    const POSITION_SHOPS = 11;

    /**
     * @var DPDBaltics
     */
    private $module;

    /**
     * @var array   Import errors
     */
    private $errors = [];

    /**
     * @var int
     */
    private $importedRowsCount = 0;
    /**
     * @var ShopRepository
     */
    private $shopRepository;

    public function __construct(DPDBaltics $module, ShopRepository $shopRepository)
    {
        $this->module = $module;
        $this->shopRepository = $shopRepository;
    }


    /**
     * {@inheritdoc}
     */
    public function importRows(array $rows)
    {
        $this->validate($rows);
        if ($this->hasErrors()) {
            return;
        }

        foreach ($rows as $row) {
            $addressTemplate = new DPDAddressTemplate();
            $addressTemplate->name = (isset($row[self::POSITION_NAME]) ? $row[self::POSITION_NAME] : '');
            $addressTemplate->type = (isset($row[self::POSITION_TYPE]) ? $row[self::POSITION_TYPE] : '');
            $addressTemplate->full_name = (isset($row[self::POSITION_FULL_NAME]) ? $row[self::POSITION_FULL_NAME] : '');
            $addressTemplate->mobile_phone_code = (isset($row[self::POSITION_PHONE_CODE]) ? $row[self::POSITION_PHONE] : '');
            $addressTemplate->mobile_phone = (isset($row[self::POSITION_PHONE]) ? $row[self::POSITION_PHONE] : '');
            $addressTemplate->email = (isset($row[self::POSITION_EMAIL]) ? $row[self::POSITION_EMAIL] : '');
            $addressTemplate->dpd_country_id = (isset($row[self::POSITION_COUNTRY_ID]) ? $row[self::POSITION_COUNTRY_ID] : '');
            $addressTemplate->zip_code = (isset($row[self::POSITION_ZIP_CODE]) ? $row[self::POSITION_ZIP_CODE] : '');
            $addressTemplate->dpd_city_name = (isset($row[self::POSITION_CITY_NAME]) ? $row[self::POSITION_CITY_NAME] : '');
            $addressTemplate->address = (isset($row[self::POSITION_ADDRESS]) ? $row[self::POSITION_ADDRESS] : '');
            $addressTemplate->save();


            $isAllShops = $row[self::POSITION_ALL_SHOPS];
            $multiValueSeparator = Configuration::get(Config::IMPORT_FIELD_MULTIPLE_SEPARATOR);
            $selectedShops = explode($multiValueSeparator, $row[self::POSITION_SHOPS]);
            $this->shopRepository->updateAddressTemplateShops($addressTemplate->id, $selectedShops, $isAllShops);
            $this->importedRowsCount++;
        }
    }

    private function validate(array $rows)
    {
        $rowNum = 0;
        foreach ($rows as $row) {
            ++$rowNum;

            if (!isset($row[self::POSITION_NAME])) {
                $this->errors[] = sprintf(
                    $this->module->l('No name set in in row %s', self::FILE_NAME),
                    $rowNum
                );
            }

            if (!isset($row[self::POSITION_TYPE])) {
                $this->errors[] = sprintf(
                    $this->module->l('No type set in in row %s', self::FILE_NAME),
                    $rowNum
                );
                return;
            }

            if ($row[self::POSITION_TYPE] !== DPDAddressTemplate::ADDRESS_TYPE_RETURN_SERVICE) {
                return;
            }

            if (!isset($row[self::POSITION_FULL_NAME])) {
                $this->errors[] = sprintf(
                    $this->module->l('No full name set in in row %s', self::FILE_NAME),
                    $rowNum
                );
                return;
            }

            if (!isset($row[self::POSITION_PHONE_CODE])) {
                $this->errors[] = sprintf(
                    $this->module->l('No phone code set in in row %s', self::FILE_NAME),
                    $rowNum
                );
                return;
            }

            if (!isset($row[self::POSITION_PHONE])) {
                $this->errors[] = sprintf(
                    $this->module->l('No phone set in in row %s', self::FILE_NAME),
                    $rowNum
                );
                return;
            }

            if (!isset($row[self::POSITION_EMAIL])) {
                $this->errors[] = sprintf(
                    $this->module->l('No email set in in row %s', self::FILE_NAME),
                    $rowNum
                );
                return;
            }

            if (!isset($row[self::POSITION_COUNTRY_ID])) {
                $this->errors[] = sprintf(
                    $this->module->l('No country ID set in in row %s', self::FILE_NAME),
                    $rowNum
                );
                return;
            }

            if (!isset($row[self::POSITION_ZIP_CODE])) {
                $this->errors[] = sprintf(
                    $this->module->l('No zip code set in in row %s', self::FILE_NAME),
                    $rowNum
                );
                return;
            }

            if (!isset($row[self::POSITION_CITY_NAME])) {
                $this->errors[] = sprintf(
                    $this->module->l('No city set in in row %s', self::FILE_NAME),
                    $rowNum
                );
                return;
            }

            if (!isset($row[self::POSITION_ADDRESS])) {
                $this->errors[] = sprintf(
                    $this->module->l('No address set in in row %s', self::FILE_NAME),
                    $rowNum
                );
                return;
            }

            if (!isset($row[self::POSITION_SHOPS]) && !isset($row[self::POSITION_ALL_SHOPS])) {
                $this->errors[] = sprintf(
                    $this->module->l('No shops set in in row %s', self::FILE_NAME),
                    $rowNum
                );
                return;
            }
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
     * @return array|void
     */
    public function deleteOldData()
    {
        Db::getInstance()->delete('dpd_address_template');
    }
}
