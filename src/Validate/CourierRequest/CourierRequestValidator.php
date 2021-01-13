<?php

namespace Invertus\dpdBaltics\Validate\CourierRequest;

use DateTime;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\DTO\CourierRequestData;

class CourierRequestValidator
{
    public function validate(CourierRequestData $courierRequestData, $countryIso)
    {
        $dateFrom = new DateTime($courierRequestData->getPickupTime());
        $dateFrom->modify('+' . Config::getMinimalTimeIntervalForCountry($countryIso) . ' minutes');
        $dateTo = new DateTime($courierRequestData->getSenderWorkUntil());

        if ($dateFrom->format('Y-m-d H:i:s') > $dateTo->format('Y-m-d H:i:s')) {
            return false;
        }

        return true;
    }
}
