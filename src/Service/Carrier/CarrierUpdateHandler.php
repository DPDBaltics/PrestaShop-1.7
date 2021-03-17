<?php

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
