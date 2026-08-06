<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UtilsController extends Controller
{
    /**
     * return a transformed mobile number in format 03XXXXXXXX or null
     */
    public static function reformatMobileNumber($number)
    {
        // length should be 14(00923xxxxxxxxx) or 13(+923xxxxxxxxx) or 11(03xxxxxxxxx)
        if (strlen($number) == 14 && self::startsWith($number, "00923")) {
            // removing 092 if present at the start and replacing with 0
            $number = "0" . substr($number, 4);
        } else if (strlen($number) == 13 && self::startsWith($number, "+923")) {
            // removing +92 if present at the start and replacing with 0
            $number = "0" . substr($number, 3);
        } else if (strlen($number) == 11 && self::startsWith($number, "03")) {
            // dont do any thing
        } else {
            return null;
        }

        // remaining number should be 11 digits long and start with 03
        if (strlen($number) != 11 || !self::startsWith($number, "03"))
            return null;

        // it should contain only digits
        if (!ctype_digit($number))
            return null;

        return $number;
    }

    static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }
}
