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


use Invertus\dpdBalticsApi\Api\DTO\Request\ParcelShopSearchRequest;
use Invertus\dpdBalticsApi\Factory\APIParamsFactory;
use Invertus\dpdBalticsApi\Factory\APIRequest\ParcelShopSearchFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class ParcelShopSearchTest extends TestCase
{
    public function testParcelShopSearch()
    {
        $countryIso = 'LV';
        $requestBody = $this->createParcelShopSearchRequest($countryIso);
        $parcelShopSearchFactory = new ParcelShopSearchFactory(
            new NullLogger(),
            new APIParamsFactory()
        );
        $parcelShopSearch = $parcelShopSearchFactory->makeParcelShopSearch();
        $responseBody = $parcelShopSearch->parcelShopSearch($requestBody);
        $this->assertEquals($responseBody->getStatus(), 'ok');
    }

    private function createParcelShopSearchRequest($countryIso)
    {
        $parcelShopSearchRequest = new ParcelShopSearchRequest($countryIso);

        return $parcelShopSearchRequest;
    }
}
