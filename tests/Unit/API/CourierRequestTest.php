<?php

use Invertus\dpdBalticsApi\Api\DTO\Request\ClosingManifestRequest;
use Invertus\dpdBalticsApi\Api\DTO\Request\CourierRequestRequest;
use Invertus\dpdBalticsApi\Factory\APIParamsFactory;
use Invertus\dpdBalticsApi\Factory\APIRequest\ClosingManifestFactory;
use Invertus\dpdBalticsApi\Factory\APIRequest\CourierRequestFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class CourierRequestTest extends TestCase
{
    public function testShipmentCreation()
    {
        $requestBody = $this->createCourierRequestRequest();
        $courierRequestFactory = new CourierRequestFactory(
            new NullLogger(),
            new APIParamsFactory()
        );
        $courierRequest = $courierRequestFactory->makeCourierRequest();
        $responseBody = $courierRequest->courierRequest($requestBody);
        $this->assertEquals($responseBody, '<p>DONE');
    }

    private function createCourierRequestRequest()
    {
        $parcelPrintRequest = new CourierRequestRequest(
            '123456',
            'testAddres',
            'senderCity',
            'LV',
            1005,
            'contactName',
            '123456789',
            '2020-02-20 18:00:00',
            '2020-02-20 15:00:00',
            15.2,
            1
        );

        return $parcelPrintRequest;
    }
}
