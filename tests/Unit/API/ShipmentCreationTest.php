<?php

use Invertus\dpdBalticsApi\Api\DTO\Request\ShipmentCreationRequest;
use Invertus\dpdBalticsApi\Factory\APIParamsFactory;
use Invertus\dpdBalticsApi\Factory\APIRequest\ShipmentCreationFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class ShipmentCreationTest extends TestCase
{
    public function testShipmentCreation()
    {
        $requestBody = $this->createShipmentCreationRequest();
        $shipmentCreatorFactory = new ShipmentCreationFactory(
            new NullLogger(),
            new APIParamsFactory()
        );
        $shipmentCreator = $shipmentCreatorFactory->makeShipmentCreation();
        $responseBody = $shipmentCreator->createShipment($requestBody);
        $this->assertEquals($responseBody->getStatus(), 'ok');
    }

    private function createShipmentCreationRequest()
    {
        $shipmentCreationRequest = new ShipmentCreationRequest(
            'testName',
            'testStreet',
            'testCity',
            'LV',
            '3003',
            1,
            'D-B2C',
            '123456',
            'test@gmail.com',
            1
        );

        return $shipmentCreationRequest;
    }
}
