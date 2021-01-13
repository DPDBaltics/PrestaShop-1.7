<?php

namespace Invertus\dpdBaltics\Service;

use Invertus\dpdBaltics\Config\Config;

class TrackingService
{
    const MAX_TRACKING_NUMBER_LENGTH= 59;

    /**
     * @param string $parcels
     * @return string
     */
    public function getTrackingNumber($parcels)
    {
        $trackingNumber = substr($parcels, 0, self::MAX_TRACKING_NUMBER_LENGTH);

        return str_replace('|', ' ', $trackingNumber);
    }
}