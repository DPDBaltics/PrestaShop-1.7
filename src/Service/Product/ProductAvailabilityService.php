<?php

namespace Invertus\dpdBaltics\Service\Product;

use Configuration;
use DPDProduct;
use Exception;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Exception\ProductAvailabilityUpdateException;
use Invertus\dpdBaltics\Exception\ProductUpdateException;
use Invertus\dpdBaltics\Repository\ProductAvailabilityRepository;
use Invertus\dpdBaltics\Repository\ProductRepository;
use Invertus\dpdBaltics\Util\ProductUtility;
use Invertus\dpdBaltics\Util\TimeZoneUtility;

class ProductAvailabilityService
{
    /**
     * @var ProductAvailabilityRepository
     */
    private $availabilityRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(
        ProductAvailabilityRepository $availabilityRepository,
        ProductRepository $productRepository
    ) {
        $this->availabilityRepository = $availabilityRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param $productReference
     * @param array $timeRanges
     * @throws ProductAvailabilityUpdateException
     */
    public function updateProductAvailabilities($productReference, array $timeRanges)
    {
        try {
            $this->availabilityRepository->deleteProductAvailabilities($productReference);
        } catch (Exception $e) {
            throw new ProductAvailabilityUpdateException($e->getMessage());
        }
        try {
            foreach ($timeRanges as $timeRange) {
                $productAvailability = new \DPDProductAvailability();
                $productAvailability->setProductReference($productReference);
                $productAvailability->setDay($timeRange['day']);
                $productAvailability->setIntervalStart($timeRange['from']);
                $productAvailability->setIntervalEnd($timeRange['to']);

                $productAvailability->add();
            }
        } catch (Exception $e) {
            throw new ProductAvailabilityUpdateException($e->getMessage());
        };
    }

    public function checkIfCarrierIsAvailable($carrierReference)
    {
        $productId = $this->productRepository->getProductIdByCarrierReference($carrierReference);
        $product = new DPDProduct($productId);
        if (!ProductUtility::hasAvailability($product->product_reference)) {
            return true;
        }
        $currentDay = date('l');
        $productAvailabilities = $this->availabilityRepository->getProductAvailabilityByReferenceAndDay(
            $product->product_reference,
            $currentDay
        );

        if (!$productAvailabilities) {
            return false;
        }

        return $this->validateTime($productAvailabilities, TimeZoneUtility::getBalticTimeZone('H:i'));
    }

    public function validateTime(array $productAvailabilities, $currentTime)
    {
        foreach ($productAvailabilities as $availability) {
            if ($availability['interval_start'] <= $currentTime &&
                $availability['interval_end'] > $currentTime) {
                return true;
            }
        }

        return false;
    }
}
