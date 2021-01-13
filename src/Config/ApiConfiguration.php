<?php

namespace Invertus\dpdBaltics\Config;

use Configuration;
use Invertus\dpdBalticsApi\Api\Configuration\ApiConfigurationInterface;
use Invertus\dpdBalticsApi\ApiConfig\ApiConfig;
use Tools;

class ApiConfiguration implements ApiConfigurationInterface
{
    public function getUrl()
    {
        if (Configuration::get(Config::SHIPMENT_TEST_MODE)) {
            $apiUrls = ApiConfig::TEST_URLS;
        } else {
            $apiUrls = ApiConfig::LIVE_URLS;
        }

        $wsCountry = Tools::strtoupper(Configuration::get(Config::WEB_SERVICE_COUNTRY));

        if (!$wsCountry || !isset($apiUrls[$wsCountry])) {
            $wsCountry = Config::LATVIA_ISO_CODE;
        }

        return $apiUrls[$wsCountry];
    }
}
