<?php


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
