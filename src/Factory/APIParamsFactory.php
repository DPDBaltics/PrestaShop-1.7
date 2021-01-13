<?php

namespace Invertus\dpdBaltics\Factory;

use Configuration;
use DPDBaltics;
use Invertus\dpdBaltics\Config\ApiConfiguration;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBalticsApi\Factory\APIParamsFactoryInterface;

class APIParamsFactory implements APIParamsFactoryInterface
{
    /**
     * @var DPDBaltics
     */
    private $module;

    /**
     * @var ApiConfiguration
     */
    private $apiConfiguration;

    public function __construct(DPDBaltics $module, ApiConfiguration $apiConfiguration)
    {
        $this->module = $module;
        $this->apiConfiguration = $apiConfiguration;
    }

    public function getUsername()
    {
        return Configuration::get(Config::WEB_SERVICE_USERNAME);
    }

    public function getPassword()
    {
        return str_rot13(Configuration::get(Config::WEB_SERVICE_PASSWORD));
    }

    public function getModuleVersion()
    {
        return $this->module->version;
    }

    public function getPSVersion()
    {
        return _PS_VERSION_;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->apiConfiguration->getUrl();
    }
}
