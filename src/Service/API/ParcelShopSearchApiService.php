<?php

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
