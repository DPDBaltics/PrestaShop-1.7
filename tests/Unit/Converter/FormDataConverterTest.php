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
