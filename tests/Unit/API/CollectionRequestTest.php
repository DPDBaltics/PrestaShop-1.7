<?php

use Invertus\dpdBalticsApi\Api\DTO\Request\CollectionRequestRequest;
use Invertus\dpdBalticsApi\Factory\APIParamsFactory;
use Invertus\dpdBalticsApi\Factory\APIRequest\CollectionRequestFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class CollectionRequestTest extends TestCase
{
    public function testShipmentCreation()
    {
        $requestBody = $this->createCollectionRequestRequest();
        $collectionRequestFactory = new CollectionRequestFactory(
            new NullLogger(),
            new APIParamsFactory()
        );
        $collectionRequest = $collectionRequestFactory->makeCollectionRequest();
        $response = $collectionRequest->collectionRequest($requestBody);
        $this->assertGreaterThan(0, strpos($response, '201 OK Process ended'));
    }

    private function createCollectionRequestRequest()
    {
        $parcelPrintRequest = new CollectionRequestRequest(
            'cname',
            'cstreet',
            'LV',
            1005,
            'city',
            'info',
            'name',
            'rstreet',
            1006,
            'LV',
            'city'
        );

        return $parcelPrintRequest;
    }
}
