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



use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Service\GoogleApiService;

class GoogleApiServiceTest extends PHPUnit_Framework_TestCase
{

//    public function testGetResultFromGoogleApiService()
//    {
//
//    }
//
    public function testGetFormattedGoogleMapsUrl()
    {
        $language = new Language(1);
        $shop = new Shop(1);
        Configuration::updateValue(Config::GOOGLE_API_KEY, 'AIzaSyCpZoxC7RTWRq0kxBAqOKAlAdaqOBYX94k');
        $googleService = new GoogleApiService($language, $shop);
        $result = $googleService->getFormattedGoogleMapsUrl();
    }
//
//    public function testGetAddressByCoordinates()
//    {
//
//    }
//
//    public function testGetDefaultCoordinates()
//    {
//
//    }

    public function testGetCoordinatesByAddress()
    {
        $language = new Language(1);
        $shop = new Shop(1);
        Configuration::updateValue(Config::GOOGLE_API_KEY, 'AIzaSyCpZoxC7RTWRq0kxBAqOKAlAdaqOBYX94k');
        $googleService = new GoogleApiService($language, $shop);
        $result = $googleService->getCoordinatesByAddress('Satekles iela 10', '', 'Ryga', 'LV-3007', 'Latvia');
    }
}
