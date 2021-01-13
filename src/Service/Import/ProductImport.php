<?php

namespace Invertus\dpdBaltics\Service\Import;

use Carrier;
use Configuration;
use DPDProduct;
use Exception;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Repository\ProductAvailabilityRepository;
use Invertus\dpdBaltics\Repository\ProductRepository;
use Invertus\dpdBaltics\Repository\ShopRepository;
use Invertus\dpdBaltics\Repository\ZoneRepository;
use Invertus\dpdBaltics\Service\Export\ProductExport;
use Language;

class ProductImport implements ImportableInterface
{

    const POSITION_NAME = 0;
    const POSITION_PRODUCT_REFERENCE = 1;
    const POSITION_IS_PUDO = 2;
    const POSITION_IS_COD = 3;
    const POSITION_IS_HOME_COLLECTION = 4;
    const POSSITION_CARRIER_NAME = 5;
    const POSSITION_CARRIER_DELAY = 6;
    const POSITION_ALL_ZONES = 7;
    const POSITION_ZONE_NAMES = 8;
    const POSITION_ALL_SHOPS = 9;
    const POSITION_SHOP_IDS = 10;
    const POSITION_ACTIVE = 11;
    const POSITION_AVAILABILITY = 12;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var array
     */
    private $errors = [];
    /**
     * @var ZoneRepository
     */
    private $zoneRepository;
    /**
     * @var ShopRepository
     */
    private $shopRepository;

    /**
     * @var array
     */
    private $warnings = [];

    /**
     * @var ProductAvailabilityRepository
     */
    private $availabilityRepository;

    public function __construct(
        ProductRepository $productRepository,
        ZoneRepository $zoneRepository,
        ShopRepository $shopRepository,
        ProductAvailabilityRepository $availabilityRepository
    ) {
        $this->productRepository = $productRepository;
        $this->zoneRepository = $zoneRepository;
        $this->shopRepository = $shopRepository;
        $this->availabilityRepository = $availabilityRepository;
    }

    /**
     * @var int
     */
    private $importedRowsCount = 0;

    /**
     * Import given array of rows
     *
     * @param array $rows
     *
     * @return void
     */
    public function importRows(array $rows)
    {
        foreach ($rows as $row) {
            $productReference = $this->oldCarrierReferenceMapping($row[self::POSITION_PRODUCT_REFERENCE]);
            $productId = $this->productRepository->getProductIdByProductReference($productReference);
            if (!$productId) {
                continue;
            }
            $product = new DPDProduct($productId);
            $product->name = $row[self::POSITION_NAME];
            $product->is_pudo = (bool)$row[self::POSITION_IS_PUDO];
            $product->is_cod = (bool)$row[self::POSITION_IS_COD];
            $product->is_home_collection = (bool)$row[self::POSITION_IS_HOME_COLLECTION];
            $product->all_zones = (bool)$row[self::POSITION_ALL_ZONES];
            $product->all_shops = (bool)$row[self::POSITION_ALL_SHOPS];
            $product->active = (bool)$row[self::POSITION_ACTIVE];

            try {
                $product->update();
            } catch (Exception $e) {
                $this->errors[] = $e->getMessage();
            }
            if (!$row[self::POSITION_IS_HOME_COLLECTION]) {
                $this->saveCarrierInfo(
                    $product->id_reference,
                    $row[self::POSSITION_CARRIER_NAME],
                    $row[self::POSSITION_CARRIER_DELAY]
                );
            }

            $this->availabilityRepository->deleteProductAvailabilities($product->product_reference);
            if (isset($row[self::POSITION_AVAILABILITY])) {
                $this->updateProductAvailability(json_decode($row[self::POSITION_AVAILABILITY]));
            }

            $productId = $this->productRepository->getProductIdByProductReference($row[self::POSITION_PRODUCT_REFERENCE]);
            $this->zoneRepository->removeZonesByProductId($productId);

            if (!$row[self::POSITION_ALL_ZONES]) {
                $zoneNames = explode(Configuration::get(Config::IMPORT_FIELD_MULTIPLE_SEPARATOR), $row[self::POSITION_ZONE_NAMES]);
                $this->saveZonesInfo($productId, $zoneNames);
            }
            if (!$row[self::POSITION_ALL_SHOPS]) {
                $shopNames = explode(Configuration::get(Config::IMPORT_FIELD_MULTIPLE_SEPARATOR), $row[self::POSITION_SHOP_IDS]);
                $this->saveShopsInfo($productId, $shopNames);
            }
            $this->importedRowsCount++;
        }
    }

    public function saveZonesInfo($productId, $zoneNames)
    {
        $zones = [];

        foreach ($zoneNames as $zoneName) {
            if (empty($zoneName)) {
                continue;
            }

            $zoneId = $this->zoneRepository->getByName($zoneName);

            if (!$zoneId) {
                continue;
            }

            $zones[] = [
                'id_dpd_product' => $productId,
                'id_dpd_zone' => $zoneId
            ];
        }

        $this->zoneRepository->addProductZonesFromArray($zones);

        return true;
    }

    public function saveShopsInfo($productId, array $shopIds)
    {
        $this->shopRepository->removeServiceCarrierShops($productId);
        $shops = [];
        foreach ($shopIds as $shopId) {
            if (empty($shopId)) {
                continue;
            }

            if (!$shopId) {
                continue;
            }
            $productId = $this->productRepository->getProductIdByProductId($productId);
            $shops[] = [
                'id_dpd_product' => $productId,
                'id_shop' => $shopId,
            ];
        }

        $this->shopRepository->addProductShopsFromArray($shops);

        return true;
    }

    public function saveCarrierInfo($carrierReference, $name, $deliveryTimes)
    {
        $separator = Configuration::get(Config::IMPORT_FIELD_MULTIPLE_SEPARATOR);
        $deliveryTimesParsed = explode($separator, $deliveryTimes);
        $delay = [];
        foreach ($deliveryTimesParsed as $deliveryTime) {
            $deliveryTimeParsed = explode(Config::MULTI_LANG_FIELD_SEPARATOR, $deliveryTime);
            $languageId = Language::getIdByIso($deliveryTimeParsed[0]);
            $delay[$languageId] = $deliveryTimeParsed[1];
        }
        $carrier = Carrier::getCarrierByReference($carrierReference);
        $carrier->name = $name;
        $carrier->delay = $delay;

        $carrier->save();
    }

    /**
     * Check if import had any errors
     *
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Get array of import errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get number of imported rows
     *
     * @return int
     */
    public function getImportedRowsCount()
    {
        return $this->importedRowsCount;
    }

    /**
     * Get import warnings
     *
     * @return array
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * Get import confirmations
     *
     * @return array
     */
    public function getConfirmations()
    {
        return [];
    }

    /**
     * Set importable to use transaction if it is supported
     *
     * @return void
     */
    public function useTransaction()
    {
        // TODO: Implement useTransaction() method.
    }

    /**
     * Delete previous data if it is relevant
     *
     * @return array
     */
    public function deleteOldData()
    {
        $this->productRepository->deleteOldData();
    }

    private function updateProductAvailability(array $productAvailabilities)
    {
        foreach ($productAvailabilities as $availability) {
            $productAvailability = new \DPDProductAvailability();
            $productAvailability->setProductReference($availability->product_reference);
            $productAvailability->setIntervalStart($availability->interval_start);
            $productAvailability->setIntervalEnd($availability->interval_end);
            $productAvailability->setDay($availability->day);
            $productAvailability->add();
        }
    }

    private function oldCarrierReferenceMapping($dpdCarrierReference)
    {
        switch ($dpdCarrierReference) {
            case 'dpd_classic':
                return Config::PRODUCT_TYPE_B2B;
            case 'dpd_pudo':
                return Config::PRODUCT_TYPE_PUDO;
            case 'dpd_classic_cod':
                return Config::PRODUCT_TYPE_B2B_COD;
            case 'dpd_pudo_cod':
                return Config::PRODUCT_TYPE_PUDO_COD;
            default:
                return $dpdCarrierReference;
        }
    }
}