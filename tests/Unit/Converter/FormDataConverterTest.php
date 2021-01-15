<?php

use Invertus\dpdBaltics\Converter\FormDataConverter;
use Invertus\dpdBaltics\DTO\ShipmentData;
use PHPUnit\Framework\TestCase;

class FormDataConverterTest extends TestCase
{
    public function testConvertShipmentFormDataToShipmentObj()
    {
        $formDataConverter = new FormDataConverter();
        $shipmentData = $formDataConverter->convertShipmentFormDataToShipmentObj($this->getTestData());
        $this->assertEquals($shipmentData, $this->getExpectedResult());
    }

    private function getTestData()
    {
        return [
            [
                'name' => 'date_shipment',
                'value' => 1
            ],
            [
                'name' => 'parcelAmount',
                'value' => 2
            ]
        ];
    }

    private function getExpectedResult()
    {
        $shipmentData = new ShipmentData();
        $shipmentData->setDateShipment(1);
        $shipmentData->setParcelAmount(2);

        return $shipmentData;
    }
}
