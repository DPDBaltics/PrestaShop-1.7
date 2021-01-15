<?php

use Invertus\dpdBalticsApi\Api\DTO\Request\ParcelPrintRequest;
use Invertus\dpdBalticsApi\Factory\APIParamsFactory;
use Invertus\dpdBalticsApi\Factory\APIRequest\ParcelPrintFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class ParcelPrintTest extends TestCase
{
    public function testParcelPrint()
    {
        $requestBody = $this->createParcelPrintRequest();
        $parcelPrinterFactory = new ParcelPrintFactory(
            new NullLogger(),
            new APIParamsFactory()
        );
        $parcelPrinter = $parcelPrinterFactory->makeParcelPrint();
        $responseBody = $parcelPrinter->printParcel($requestBody);
        $this->assertEquals($responseBody->getStatus(), 'ok');
    }

    private function createParcelPrintRequest()
    {
        $parcelPrintRequest = new ParcelPrintRequest(
            '05757922204560'
        );

        return $parcelPrintRequest;
    }
}
