<?php

namespace Invertus\dpdBaltics\Service\Carrier;

use Carrier;
use DPDPrestashopCarrier;
use DPDProduct;
use Group;
use Invertus\dpdBaltics\Builder\CarrierBuilder;
use Invertus\dpdBaltics\Builder\CarrierImageBuilder;
use Invertus\dpdBaltics\Collection\DPDProductInstallCollection;
use Invertus\dpdBaltics\DTO\DPDProductInstall;
use Invertus\dpdBaltics\Repository\ProductRepository;
use Language;
use PrestaShopDatabaseException;
use PrestaShopException;
use Validate;
use Zone;

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

class CreateCarrierService
{
    /**
     * @var CarrierBuilder
     */
    private $carrierBuilder;

    /**
     * @var CarrierImageBuilder
     */
    private $carrierImageBuilder;


    /**
     * @var array
     */
    private $errors = [];
    /**
     * @var Language
     */
    private $language;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * DPDCreateCarriersService constructor.
     *
     * @param Language $language
     * @param CarrierBuilder $carrierBuilder
     * @param CarrierImageBuilder $carrierImageBuilder
     */
    public function __construct(
        Language $language,
        CarrierBuilder $carrierBuilder,
        CarrierImageBuilder $carrierImageBuilder,
        ProductRepository $productRepository
    ) {
        $this->language = $language;
        $this->carrierBuilder = $carrierBuilder;
        $this->carrierImageBuilder = $carrierImageBuilder;
        $this->productRepository = $productRepository;
    }

    /**
     * Create prestashop carriers for given service IDs
     * and map them to services in the database for later reference.
     *
     * @param DPDProductInstallCollection $products
     * @return bool result
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function createCarriers(DPDProductInstallCollection $products)
    {
        $psZones = Zone::getZones();
        $psGroups = Group::getGroups($this->language->id);
        $psGroupIds = [];

        foreach ($psGroups as $psGroup) {
            $psGroupIds[] = $psGroup['id_group'];
        }

        $carrierBuilder = $this->carrierBuilder;
        $carrierImageBuilder = $this->carrierImageBuilder;

        /**
         * @var $product DPDProductInstall
         */
        foreach ($products as $product) {
            $idCarrier = null;
            $carrier = new DPDPrestashopCarrier($idCarrier);

            // carrier exist and it's data is updated
            $updateAction = false;
            if (Validate::isLoadedObject($carrier)) {
                $updateAction = true;
            }
            $currentSavedProduct = $this->productRepository->findProductByProductReference($product->getId());
            if ($currentSavedProduct) {
                $carrierBuilder->setActive($currentSavedProduct['active']);
                $carrierBuilder->setName($currentSavedProduct['name']);
            } else {
                $carrierBuilder->setActive(0);
                $carrierBuilder->setName($product->getName());
            }
            $carrierBuilder->setIdCarrier($idCarrier);
            $carrierBuilder->setShippingMethod(Carrier::SHIPPING_METHOD_WEIGHT);
            $carrierBuilder->setDelay($this->fillCarrierMultilangField($product->getDelay()));
            $carrierBuilder->setShippingExternal(1);
            $carrierBuilder->setZones($psZones);
            $carrierBuilder->setGroups($psGroupIds);
            $carrierBuilder->setIsModule(true);
            $carrierBuilder->setNeedRange(true);

            $carrier = $carrierBuilder->save();

            foreach ($carrierBuilder->getErrors() as $error) {
                $this->errors[] = $error;
            }
            // Can use ID as reference because we just added the carrier
            // but if carrier is being updated we use the id reference
            $carrierReference = (new Carrier($carrier->id))->id_reference;

            $carrierImageBuilder->setIdReference($carrierReference);
            $carrierImageBuilder->setIsPickupCarrier(false);
            $carrierImageBuilder->save();


            if (isset($currentSavedProduct)) {
                $dpdProduct = new DPDProduct((int)$currentSavedProduct['id_dpd_product']);
                $dpdProduct->id_reference = $carrierReference;
            } else {
                $dpdProduct = new DPDProduct();
                $dpdProduct->name = $product->getName();
                $dpdProduct->product_reference = $product->getId();
                $dpdProduct->id_reference = $carrierReference;
                $dpdProduct->is_home_collection = 0;
                $dpdProduct->active = 0;
                $dpdProduct->all_zones = 0;
                $dpdProduct->all_shops = 0;
                $dpdProduct->is_pudo = $product->getIsPudo();
                $dpdProduct->is_cod = $product->getIsCod();
            }
            $dpdProduct->save();
        }

        return true;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    /** generates multi language fields for carriers
     * @param $serviceId - service id
     * @param $id - unique identifier to identify same carriers
     * @return array
     */
    private function fillCarrierMultilangField($delay)
    {
        $values = [];
        foreach (Language::getLanguages() as $language) {
            $values[$language['id_lang']] = $delay;
        }

        return $values;
    }
}
