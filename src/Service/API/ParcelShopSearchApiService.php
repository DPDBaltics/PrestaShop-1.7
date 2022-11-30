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


namespace Invertus\dpdBaltics\Service\API;

use Cache;
use Configuration;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBalticsApi\Api\DTO\Request\ParcelShopSearchRequest;
use Invertus\dpdBalticsApi\Factory\APIRequest\ParcelShopSearchFactory;

class ParcelShopSearchApiService
{
    /**
     * @var ParcelShopSearchFactory
     */
    private $parcelShopSearchFactory;

    public function __construct(ParcelShopSearchFactory $parcelShopSearchFactory)
    {
        $this->parcelShopSearchFactory = $parcelShopSearchFactory;
    }

    public function getAllCountryParcels(
        $iso,
        $fetchPudoPoints,
        $retrieveOpeningHours
    ) {
        $requestBody = $this->createParcelSearchRequest(
            $iso,
            $fetchPudoPoints,
            $retrieveOpeningHours,
            null,
            null,
            null
        );
        $parcelShopSearch = $this->parcelShopSearchFactory->makeParcelShopSearch();

        $response = $parcelShopSearch->parcelShopSearch($requestBody);

        if ($response->getStatus() === Config::API_SUCCESS_STATUS && is_array($response->getParcelShops())) {
            return $response;
        }

        return $response;
    }

    private function createParcelSearchRequest(
        $iso,
        $fetchPudoPoints,
        $retrieveOpeningHours,
        $city,
        $pCode,
        $address
    ) {
        $shopSearchRequest = new ParcelShopSearchRequest($iso, $fetchPudoPoints, $retrieveOpeningHours);
        $shopSearchRequest->setCity($city);
        $shopSearchRequest->setPCode($pCode);
        $shopSearchRequest->setStreet($address);

        return $shopSearchRequest;
    }
}
