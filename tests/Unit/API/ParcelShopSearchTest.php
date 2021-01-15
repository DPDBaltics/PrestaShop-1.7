<?php

use Invertus\dpdBalticsApi\Api\DTO\Request\ParcelShopSearchRequest;
use Invertus\dpdBalticsApi\Factory\APIParamsFactory;
use Invertus\dpdBalticsApi\Factory\APIRequest\ParcelShopSearchFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class ParcelShopSearchTest extends TestCase
{
    public function testParcelShopSearch()
    {
        $countryIso = 'LV';
        $requestBody = $this->createParcelShopSearchRequest($countryIso);
        $parcelShopSearchFactory = new ParcelShopSearchFactory(
            new NullLogger(),
            new APIParamsFactory()
        );
        $parcelShopSearch = $parcelShopSearchFactory->makeParcelShopSearch();
        $responseBody = $parcelShopSearch->parcelShopSearch($requestBody);
        $this->assertEquals($responseBody->getStatus(), 'ok');
    }

    private function createParcelShopSearchRequest($countryIso)
    {
        $parcelShopSearchRequest = new ParcelShopSearchRequest($countryIso);

        return $parcelShopSearchRequest;
    }
}
