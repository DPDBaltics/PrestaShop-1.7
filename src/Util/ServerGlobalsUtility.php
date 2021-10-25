<?php
declare(strict_types=1);


namespace Invertus\dpdBaltics\Util;


class ServerGlobalsUtility
{
    public static function getHttpReferer()
    {
        return $_SERVER['HTTP_REFERER'] ?: null;
    }
}