<?php

namespace Invertus\dpdBaltics\Util;

use Configuration;
use Invertus\dpdBaltics\Config\Config;

class CountryUtility
{
    public static function isEstonia()
    {
        return Configuration::get(Config::WEB_SERVICE_COUNTRY) === Config::ESTONIA_ISO_CODE;
    }
}