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


namespace Invertus\dpdBaltics\Service\Carrier;


use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Repository\ProductRepository;
use Invertus\dpdBaltics\Service\Product\ProductService;

class CarrierUpdateHandler
{
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var UpdateCarrierService
     */
    private $updateCarrierService;
    /**
     * @var ProductService
     */
    private $productService;

    public function __construct(
        ProductRepository $productRepository,
        UpdateCarrierService $updateCarrierService,
        ProductService $productService
    ) {
        $this->productRepository = $productRepository;
        $this->updateCarrierService = $updateCarrierService;
        $this->productService = $productService;
    }

    /**
     * @param $webServiceCountry
     *
     * @throws \Invertus\dpdBaltics\Exception\ProductUpdateException
     */
    public function updateCarrierName($webServiceCountry)
    {
        $products = Config::getProducts($webServiceCountry);

        foreach ($products as $product) {
           $productId = $this->productRepository->getProductIdByProductReference($product->getId());
           $this->updateCarrierService->updateCarrierName($productId, $product->getName());
           $this->productService->updateProductName($productId, $product->getName());
        }
    }
}
