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
