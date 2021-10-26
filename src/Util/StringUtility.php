<?php
declare(strict_types=1);


namespace Invertus\dpdBaltics\Util;


class StringUtility
{
    public static function toLowerCase($string)
    {
        return \Tools::strtolower($string);
    }

}