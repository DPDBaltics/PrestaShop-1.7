<?php

declare(strict_types=1);

namespace Invertus\dpdBaltics\Util;

class NumberUtility
{
   public static function isValidPhone($number)
   {
       return preg_match('/^[6-9]\d{9}$/', $number);
   }

   public static function isValidLength($number, $minLength, $maxLength)
    {
        return (bool) (strlen($number) > $minLength && strlen($number) < $maxLength);
    }
}
