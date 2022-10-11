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
