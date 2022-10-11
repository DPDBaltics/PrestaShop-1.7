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
