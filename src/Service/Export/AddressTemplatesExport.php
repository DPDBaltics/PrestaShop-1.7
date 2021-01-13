<?php

namespace Invertus\dpdBaltics\Service\Export;

use Configuration;
use DPDAddressTemplate;
use DPDBaltics;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Repository\AddressTemplateRepository;
use PrestaShopCollection;

class AddressTemplatesExport implements ExportableInterface
{
    const FILE_NAME = 'AddressTemplatesExport';

    /**
     * @var array|int[] Address templates to export
     */
    private $idAddresses = [];

    /**
     * @var DPDBaltics
     */
    private $module;
    /**
     * @var AddressTemplateRepository
     */
    private $templateRepository;

    /**
     * DPDAddressTemplatesExport constructor.
     *
     * @param DPDBaltics $module
     * @param AddressTemplateRepository $templateRepository
     */
    public function __construct(DPDBaltics $module, AddressTemplateRepository $templateRepository)
    {
        $this->module = $module;
        $this->templateRepository = $templateRepository;
    }

    public function getRows()
    {
        $addressesIds = $this->templateRepository->getAllAddressTemplateIds();

        $rows = [];

        foreach ($addressesIds as $addressesId) {
            $address = new DPDAddressTemplate($addressesId);

            // collect data for shops
            $separator = Configuration::get(Config::EXPORT_FIELD_MULTIPLE_SEPARATOR);
            $implodedShopIds = '';
            $isAllShops = $this->templateRepository->findIsAllShopsAssigned($addressesId);
            if (!$isAllShops) {
                $ShopsIds = $this->templateRepository->findAllShopsAssigned($addressesId);
                $implodedShopIds = implode($separator, $ShopsIds);
            }

            $rows[] = [
                $address->name,
                $address->type,
                $address->full_name,
                $address->mobile_phone_code,
                $address->mobile_phone,
                $address->email,
                $address->dpd_country_id,
                $address->zip_code,
                $address->dpd_city_name,
                $address->address,
                $isAllShops,
                $implodedShopIds
            ];
        }

        return $rows;
    }

    public function getHeaders()
    {
        return [
            $this->module->l('Name', self::FILE_NAME),
            $this->module->l('Address type', self::FILE_NAME),
            $this->module->l('Full name.', self::FILE_NAME),
            $this->module->l('GSM code', self::FILE_NAME),
            $this->module->l('Mobile phone', self::FILE_NAME),
            $this->module->l('Email address', self::FILE_NAME),
            $this->module->l('Country id', self::FILE_NAME),
            $this->module->l('Zip code', self::FILE_NAME),
            $this->module->l('City name', self::FILE_NAME),
            $this->module->l('Address', self::FILE_NAME),
            $this->module->l('All shops', self::FILE_NAME),
            $this->module->l('Shops', self::FILE_NAME),
        ];
    }

    public function getFileName()
    {
        return sprintf(Config::IMPORT_EXPORT_OPTION_ADDRESS_TEMPLATES . '_%s.csv', date('Y-m-d_His'));
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
