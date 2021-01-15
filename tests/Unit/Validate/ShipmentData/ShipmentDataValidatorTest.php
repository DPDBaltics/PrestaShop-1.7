<?php

use Invertus\dpdBaltics\DTO\ShipmentData;
use Invertus\dpdBaltics\Validate\ShipmentData\Exception\InvalidShipmentDataField;
use Invertus\dpdBaltics\Validate\ShipmentData\ShipmentDataValidator;

class ShipmentDataValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider invalidProductDataProvider
     */
    public function testInvalidProduct($product)
    {
        $shipmentDataValidator = new ShipmentDataValidator();
        $shipmentData = new ShipmentData();
        $shipmentData->setProduct($product);

        $this->expectException(InvalidShipmentDataField::class);
        $shipmentDataValidator->validate($shipmentData);
    }

    public function invalidProductDataProvider()
    {
        return [
            [
                'product' => null,
            ],
            [
                'product' => 0
            ]
        ];
    }
}