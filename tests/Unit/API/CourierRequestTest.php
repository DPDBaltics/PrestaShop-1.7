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
