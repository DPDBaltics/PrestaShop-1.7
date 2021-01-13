<?php

namespace Invertus\dpdBaltics\Service\Product;

use DPDProduct;
use Exception;
use Invertus\dpdBaltics\Exception\ProductUpdateException;
use Invertus\dpdBaltics\Repository\ProductShopRepository;

class UpdateProductShopService
{
    /**
     * @var ProductShopRepository
     */
    private $productShopRepository;

    public function __construct(ProductShopRepository $productShopRepository)
    {
        $this->productShopRepository = $productShopRepository;
    }

    /**
     * @param $productId
     * @param array $shops
     *
     * @throws ProductUpdateException
     */
    public function updateProductShop($productId, array $shops)
    {
        try {
            $dpdProduct = new DPDProduct($productId);
            $this->productShopRepository->deleteProductShops($productId);
            $dpdProduct->all_shops = 0;

            foreach ($shops as $shop) {
                if ((int)$shop === 0) {
                    $dpdProduct->all_shops = 1;
                    $dpdProduct->update();
                    return;
                }

                $dpdProduct->update();
                $this->productShopRepository->insertProductShop($productId, $shop);
            }
        } catch (Exception $e) {
            throw new ProductUpdateException($e->getMessage());
        };
    }
}
