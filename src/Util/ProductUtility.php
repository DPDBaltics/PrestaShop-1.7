<?php

namespace Invertus\dpdBaltics\Util;

use Invertus\dpdBaltics\Config\Config;

class ProductUtility
{
    public static function hasAvailability($productReference) {
        if ($productReference === Config::PRODUCT_TYPE_SATURDAY_DELIVERY ||
            $productReference === Config::PRODUCT_TYPE_SATURDAY_DELIVERY_COD ||
            $productReference === Config::PRODUCT_TYPE_SAME_DAY_DELIVERY) {
            return true;
        }

        return false;
    }

    public static function validateSameDayDelivery($countryIso, $city)
    {
        if ($countryIso !== Config::LATVIA_ISO_CODE) {
            return false;
        }

        if (strtolower($city) === 'riga' || strtolower($city) === 'rīga') {
            return true;
        }

        return false;
    }

}