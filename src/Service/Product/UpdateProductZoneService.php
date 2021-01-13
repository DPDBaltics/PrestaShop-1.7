<?php

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
