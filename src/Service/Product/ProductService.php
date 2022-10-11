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


namespace Invertus\dpdBaltics\Service\Product;

use Carrier;
use DPDBaltics;
use DPDProduct;
use Exception;
use Invertus\dpdBaltics\Collection\DPDProductInstallCollection;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Exception\ProductUpdateException;
use Invertus\dpdBaltics\Repository\ProductRepository;
use Invertus\dpdBaltics\Service\Carrier\CreateCarrierService;
use PrestaShop\PrestaShop\Adapter\Validate;

class ProductService
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var CreateCarrierService
     */
    private $createCarrierService;

    public function __construct(
        ProductRepository $productRepository,
        CreateCarrierService $createCarrierService
    ) {
        $this->productRepository = $productRepository;
        $this->createCarrierService = $createCarrierService;
    }

    /**
     * @param $productId
     * @param $active
     *
     * @param $isCod
     * @throws ProductUpdateException
     */
    public function updateProduct($productId, $active)
    {
        try {
            $dpdProduct = new DPDProduct($productId);
            $dpdProduct->active = $active;
            $dpdProduct->update();
        } catch (Exception $e) {
            throw new ProductUpdateException($e->getMessage());
        }
    }

    /**
     * @param $productId
     * @param $name
     *
     * @throws ProductUpdateException
     */
    public function updateProductName($productId, $name)
    {

        try {
            $dpdProduct = new DPDProduct($productId);
            if (!\Validate::isLoadedObject($dpdProduct)) {
                return;
            }
            $dpdProduct->name = $name;
            $dpdProduct->update();
        } catch (Exception $e) {
            throw new ProductUpdateException($e->getMessage());
        }
    }

    public function deleteProduct($productReference)
    {
        $productId = $this->productRepository->getProductIdByProductReference($productReference);

        if (!$productId) {
            return true;
        }
        $product = new DPDProduct($productId);
        $carrier = Carrier::getCarrierByReference($product->id_reference);
        if (!Validate::isLoadedObject($carrier)) {
            return true;
        }

        $carrier->deleted = 1;
        $carrier->update();

        return $this->productRepository->deleteByProductReference($product->getProductReference());
    }

    public function addProduct($productReference, $countryCode = null)
    {
        $collection = new DPDProductInstallCollection();
        $product = Config::getProductByReference($productReference, $countryCode);
        $collection->add($product);

        return $this->createCarrierService->createCarriers($collection);
    }


    public function updateCarriersOnCountryChange($newCountryIsoCode)
    {
        $productId = $this->productRepository->getProductIdByProductReference(
            Config::PRODUCT_TYPE_PUDO_COD
        );
        if (!$productId) {
            $this->addProduct(Config::PRODUCT_TYPE_PUDO_COD, $newCountryIsoCode);
        }

        $productId = $this->productRepository->getProductIdByProductReference(
            Config::PRODUCT_TYPE_SAME_DAY_DELIVERY
        );
        if ($newCountryIsoCode !== Config::LATVIA_ISO_CODE) {
            $this->deleteProduct(Config::PRODUCT_TYPE_SAME_DAY_DELIVERY);
        } elseif (!$productId) {
            $this->addProduct(Config::PRODUCT_TYPE_SAME_DAY_DELIVERY, $newCountryIsoCode);
        }

        $productId = $this->productRepository->getProductIdByProductReference(
            Config::PRODUCT_TYPE_SATURDAY_DELIVERY_COD
        );
        if (!$productId) {
            $this->addProduct(Config::PRODUCT_TYPE_SATURDAY_DELIVERY_COD, $newCountryIsoCode);
        }

        return true;
    }
}
