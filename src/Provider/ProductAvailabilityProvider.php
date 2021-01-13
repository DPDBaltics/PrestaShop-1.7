<?php


namespace Invertus\dpdBaltics\Provider;

use Country;
use Exception;
use Invertus\dpdBaltics\Repository\ProductAvailabilityRepository;
use Language;
use DPDZone;
use Tools;
use Validate;

class ProductAvailabilityProvider
{
    /**
     * @var Language
     */
    private $language;

    /**
     * @var ProductAvailabilityRepository
     */
    private $availabilityRepository;

    public function __construct(Language $language, ProductAvailabilityRepository $availabilityRepository)
    {
        $this->language = $language;
        $this->availabilityRepository = $availabilityRepository;
    }

    /**
     * Get zone ranges for Javascript
     *
     * @return array
     */
    public function getProductAvailabilityForJS($productId)
    {
        $jsProductAvailabilityRanges = [];

        try {
            $product = new \DPDProduct($productId);

            if (!Validate::isUnsignedId($productId) || !Validate::isLoadedObject($product)) {
                return $jsProductAvailabilityRanges;
            }
            $productAvailabilities = $this->availabilityRepository->getProductAvailabilityByReference($product->getProductReference());
        } catch (Exception $e) {
            return $jsProductAvailabilityRanges;
        }

        foreach ($productAvailabilities as $availability) {
            $jsProductAvailabilityRanges[] = [
                'id' => $availability['id_dpd_product_availability'],
                'from' => $availability['interval_start'],
                'to' => $availability['interval_end'],
                'day' => $availability['day'],
            ];
        }

        return $jsProductAvailabilityRanges;
    }
}
