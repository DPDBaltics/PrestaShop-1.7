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
