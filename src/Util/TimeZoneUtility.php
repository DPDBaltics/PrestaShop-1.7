<?php

namespace Invertus\dpdBaltics\Util;

use DateInterval;
use DateTime;
use DateTimeZone;
use Invertus\dpdBaltics\Config\Config;

class TimeZoneUtility
{
    public static function getBalticTimeZone($format = 'H:i')
    {
        $today = self::getBalticTimeZoneAsDateTime();

        return $today->format($format);
    }

    public static function getCourierDefaultPickUpTime()
    {
        $currentTime = self::getBalticTimeZone();
        switch ($currentTime) {
            case $currentTime <= Config::COURIER_SAME_DAY_TIME_LIMITATION:
                /** @var DateTime $deliveryTimeFrom */
                $deliveryTimeFrom = self::getBalticTimeZoneAsDateTime();
                $deliveryTimeFrom->modify('+' . Config::COURIER_SAME_DAY_TIME_ADDITIONAL_MINUTES .' minutes');

                return $deliveryTimeFrom->format('Y-m-d H:i:s');
            default:
                /** @var DateTime $deliveryTimeFrom */
                $deliveryTimeFrom = self::getBalticTimeZoneAsDateTime();
                $deliveryTimeFrom->modify('+1 day');

                return $deliveryTimeFrom->format('Y-m-d 08:00:00');
        }
    }

    public static function getCourierDefaultWorkUntil()
    {
        $currentTime = self::getBalticTimeZone();
        switch ($currentTime) {
            case $currentTime < Config::COURIER_SAME_DAY_TIME_LIMITATION:
                /** @var DateTime $deliveryTimeFrom */
                $deliveryTimeFrom = self::getBalticTimeZoneAsDateTime();
                $deliveryTimeFrom->modify('+' . Config::COURIER_SAME_DAY_TIME_ADDITIONAL_MINUTES .' minutes');

                return $deliveryTimeFrom->format('Y-m-d 17:00:00');
            default:
                /** @var DateTime $deliveryTimeFrom */
                $deliveryTimeFrom = self::getBalticTimeZoneAsDateTime();
                $deliveryTimeFrom->modify('+1 day');

                return $deliveryTimeFrom->format('Y-m-d 17:00:00');
        }
    }

    public static function getBalticTimeZoneAsDateTime()
    {
        $tz = 'Europe/Vilnius';
        $tz_obj = new DateTimeZone($tz);

        return new DateTime("now", $tz_obj);
    }
}