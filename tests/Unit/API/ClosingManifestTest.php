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


use Invertus\dpdBalticsApi\Api\DTO\Request\ClosingManifestRequest;
use Invertus\dpdBalticsApi\Api\DTO\Request\ShipmentCreationRequest;
use Invertus\dpdBalticsApi\Factory\APIParamsFactory;
use Invertus\dpdBalticsApi\Factory\APIRequest\ClosingManifestFactory;
use Invertus\dpdBalticsApi\Factory\APIRequest\ShipmentCreationFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class ClosingManifestTest extends TestCase
{
    public function testShipmentCreation()
    {
        $requestBody = $this->createShipmentCreationRequest();
        $shipmentCreationFactory = new ShipmentCreationFactory(
            new NullLogger(),
            new APIParamsFactory()
        );
        $shipmentCreator = $shipmentCreationFactory->makeShipmentCreation();
        $shipmentCreator->createShipment($requestBody);

        $requestBody = $this->createClosingManifestRequest();
        $closingManifestFactory = new ClosingManifestFactory(
            new NullLogger(),
            new APIParamsFactory()
        );
        $closingManifest = $closingManifestFactory->makeClosingManifest();
//        $responseBody = $closingManifest->closeManifest($requestBody);
//        $this->assertEquals($responseBody->getStatus(), 'ok');
    }

    private function createClosingManifestRequest()
    {
        $parcelPrintRequest = new ClosingManifestRequest(
            date('Y-m-d')
        );

        return $parcelPrintRequest;
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
