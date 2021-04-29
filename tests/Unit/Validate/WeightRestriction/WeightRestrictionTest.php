<?php


use Invertus\dpdBaltics\Validate\Weight\CartWeightValidator;

class WeightRestrictionTest extends PHPUnit_Framework_TestCase
{

    public function testParcelDistributionSameShipmentWeight()
    {
        //All products in same shipment
        $distribution = 'none';

        $cartMock = $this->getCartMock();
        $cartMock->method('getTotalWeight')->willReturn($this->getMockTotalWeight());

        $validator = new CartWeightValidator();

        $this->assertTrue($validator->validate($cartMock, $distribution, $this->getMaxAllowedWeight()));

    }

    public function testParcelDistributionProductInSeparateParcel()
    {
        //Each product in separate parcel
        $distribution = 'parcel_product';
        $validator = new CartWeightValidator();

        $cartMock = $this->getCartMock();
        $cartMock->method('getProducts')->willReturn($this->getMockProducts());

        $this->assertFalse($validator->validate($cartMock, $distribution, $this->getMaxAllowedWeight()));

    }

    public function testParcelDistributionQuantityInSeparateParcel()
    {
        //Each quantity in separate parcel
        $distribution = 'parcel_quantity';
        $validator = new CartWeightValidator();

        $cartMock = $this->getCartMock();
        $cartMock->method('getProducts')->willReturn($this->getMockProducts());

        $this->assertTrue($validator->validate($cartMock, $distribution, $this->getMaxAllowedWeight()));

    }

    private function getMockTotalWeight()
    {
        return 10;
    }

    private function getMaxAllowedWeight()
    {
        return 11;
    }

    private function getCartMock()
    {
        return $this->getMockBuilder(\Cart::class)
            ->setMethods([
                'getTotalWeight',
                'getProducts'
            ])
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    private function getMockProducts()
    {
        return [
            0 => [
                'weight' => 5,
                'quantity' => 2
            ],
            1 => [
                'weight' => 10,
                'quantity' => 2
            ]
        ];
    }
}
