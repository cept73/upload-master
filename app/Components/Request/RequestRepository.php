<?php

namespace App\Components\Request;

class RequestRepository
{
    public static function get(string $key, bool $encoded = false)
    {
        $value = request()->header($key);
        if ($encoded) {
            $value = urldecode($value);
        }

        return $value;
    }
}
