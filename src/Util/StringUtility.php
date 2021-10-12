<?php

declare(strict_types=1);

namespace Invertus\dpdBaltics\Util;

class StringUtility
{
    public static function toLowerCase($string)
    {
        return \Tools::strtolower($string);
    }

    public static function isWordInString($prefix, $number)
    {
        return (bool) strpos($number, $prefix) !== false;
    }
}