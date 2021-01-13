<?php

namespace Invertus\dpdBaltics\Service\Export;

use Carrier;
use Configuration;
use DPDBaltics;
use DPDProduct;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Repository\ProductAvailabilityRepository;
use Invertus\dpdBaltics\Repository\ProductRepository;
use Invertus\dpdBaltics\Repository\ProductShopRepository;
use Invertus\dpdBaltics\Repository\ZoneRepository;
use Language;

class ProductExport implements ExportableInterface
{

    const FILE_NAME = 'ProductExport';

    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var ZoneRepository
     */
    private $zoneRepository;
    /**
     * @var ProductShopRepository
     */
    private $shopRepository;
    /**
     * @var DPDBaltics
     */
    private $module;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var ProductAvailabilityRepository
     */
    private $availabilityRepository;

    public function __construct(
        DPDBaltics $module,
        ProductRepository $productRepository,
        ZoneRepository $zoneRepository,
        ProductShopRepository $shopRepository,
        ProductAvailabilityRepository $availabilityRepository
    ) {
        $this->productRepository = $productRepository;
        $this->zoneRepository = $zoneRepository;
        $this->shopRepository = $shopRepository;
        $this->module = $module;
        $this->availabilityRepository = $availabilityRepository;
    }

    /**
     * Get data to export
     *
     * @return array
     * @throws \PrestaShopDatabaseException
     */
    public function getRows()
    {
        $separator = Configuration::get(Config::EXPORT_FIELD_MULTIPLE_SEPARATOR);

        $products = $this->productRepository->getAllProducts();
        /** @var DPDProduct $product */
        foreach ($products as $product) {
            $zoneNames = [];
            $hasAllZones = false;
            $zones = $this->zoneRepository->findZonesIdsByCarrierReference($product->id_reference);
            foreach ($zones as $zone) {
                if (isset($zone['all_zones']) && $zone['all_zones']) {
                    $hasAllZones = true;
                    break;
                }

                $zoneNames[] = $zone['name'];
            }

            $shops = $this->shopRepository->getProductShopsByReference($product->id);
            $shopIds = [];
            $hasAllShops = false;

            if ($product->all_shops) {
                $hasAllShops = true;
            } else {
                foreach ($shops as $shop) {
                    if (isset($shop['all_shops']) && $shop['all_shops']) {
                        $hasAllShops = true;
                        break;
                    }

                    $shopIds[] = $shop['id_shop'];
                }
            }

            $carrier = Carrier::getCarrierByReference($product->id_reference);
            $productAvailability = $this->availabilityRepository->getProductAvailabilityByReference(
                $product->getProductReference()
            );

            $rows[] = [
                $product->name,
                $product->product_reference,
                $product->is_pudo,
                $product->is_cod,
                $product->is_home_collection,
                $carrier->name,
                $this->getDeliveryTimes($product->id_reference),
                $hasAllZones,
                !empty($zoneNames) && !$hasAllZones ? implode($separator, $zoneNames) : '',
                $hasAllShops,
                !empty($shopIds) && !$hasAllShops ? implode($separator, $shopIds) : '',
                $product->active,
                json_encode($productAvailability)
            ];
        }

        return $rows;
    }

    /**
     * Get export file name
     *
     * @return string
     */
    public function getFileName()
    {
        return sprintf(Config::IMPORT_EXPORT_OPTION_PRODUCTS . '_%s.csv', date('Y-m-d_His'));
    }

    /**
     * Get array of headers
     *
     * @return array|string
     */
    public function getHeaders()
    {
        return [
            $this->module->l('Name', self::FILE_NAME),
            $this->module->l('Product reference', self::FILE_NAME),
            $this->module->l('Is pudo', self::FILE_NAME),
            $this->module->l('Is COD', self::FILE_NAME),
            $this->module->l('Is Home Collection', self::FILE_NAME),
            $this->module->l('Carrier name', self::FILE_NAME),
            $this->module->l('Delivery time', self::FILE_NAME),
            $this->module->l('All zones', self::FILE_NAME),
            $this->module->l('Zone names', self::FILE_NAME),
            $this->module->l('All shops', self::FILE_NAME),
            $this->module->l('Shop ids', self::FILE_NAME),
            $this->module->l('Active', self::FILE_NAME),
            $this->module->l('Availability', self::FILE_NAME),
        ];
    }

    /**
     * checks if has errors
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * gets array of errors
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    private function getDeliveryTimes($carrierReference)
    {
        $separator = Configuration::get(Config::EXPORT_FIELD_MULTIPLE_SEPARATOR);
        $carrier = Carrier::getCarrierByReference($carrierReference);

        $languages = Language::getLanguages(false);
        $result = [];
        foreach ($languages as $language) {
            if (!isset($carrier->delay[$language['id_lang']])) {
                continue;
            }

            $delayName = $carrier->delay[$language['id_lang']];
            if (false !== strpos($delayName, $separator)) {
                $this->errors[] =
                    sprintf(
                        $this->module->l('Carrier %s contains illegal character %s for delay name %s in language %s. Please change export separator value or carrier delay name', self::FILE_NAME),
                        $carrier->name,
                        $separator,
                        $delayName,
                        $language['name']
                    );
                continue;
            }

            $result[] = $this->getMultiLangFieldFormat($language['iso_code'], $delayName);
        }

        return implode($separator, $result);
    }

    public function getMultiLangFieldFormat($isoCode, $name)
    {
        return sprintf(
            '%s' . config::MULTI_LANG_FIELD_SEPARATOR . '%s',
            $isoCode,
            $name
        );
    }
}