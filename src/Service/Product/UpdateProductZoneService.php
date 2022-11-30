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
use Invertus\dpdBaltics\Repository\ProductZoneRepository;

class UpdateProductZoneService
{
    /**
     * @var ProductZoneRepository
     */
    private $productZoneRepository;

    public function __construct(ProductZoneRepository $productZoneRepository)
    {
        $this->productZoneRepository = $productZoneRepository;
    }

    /**
     * @param $productId
     * @param array $zones
     *
     * @throws ProductUpdateException
     */
    public function updateProductZones($productId, array $zones)
    {
        try {
            $dpdProduct = new DPDProduct($productId);
            $this->productZoneRepository->deleteProductZones($productId);
            $dpdProduct->all_zones = 0;

            foreach ($zones as $zone) {
                if ((int)$zone === 0) {
                    $dpdProduct->all_zones = 1;
                    $dpdProduct->update();
                    return;
                }

                $dpdProduct->update();
                $this->productZoneRepository->insertProductZone($productId, $zone);
            }
        } catch (Exception $e) {
            throw new ProductUpdateException($e->getMessage());
        };
    }
}
