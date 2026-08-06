<?php

if (!function_exists('numberToWords')) {
    function numberToWords($number)
    {
        $words = array(
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'forty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
        );

        $levels = array(
            100 => 'hundred',
            1000 => 'thousand',
            100000 => 'lakh',      // Added lakh
            10000000 => 'crore',   // Added crore
        );

        if ($number == 0) {
            return $words[0];
        }

        if (strpos($number, '.') !== false) {
            // Handle decimals
            $parts = explode('.', $number);
            $wholePart = intval($parts[0]);
            $decimalPart = intval($parts[1]);

            $wholePartWords = numberToWords($wholePart);
            $decimalPartWords = $decimalPart > 0 ? numberToWords($decimalPart) . ' cents' : '';

            return trim($wholePartWords . ($decimalPartWords ? ' and ' . $decimalPartWords : '') . ' only');
        }

        $output = '';

        foreach (array_reverse($levels, true) as $value => $label) {
            if ($number >= $value) {
                $count = floor($number / $value);
                $output .= numberToWords($count) . ' ' . $label . ' ';
                $number %= $value;
            }
        }

        if ($number > 0) {
            if ($number < 21) {
                $output .= $words[$number];
            } elseif ($number < 100) {
                $output .= $words[floor($number / 10) * 10];
                if ($number % 10) {
                    $output .= '-' . $words[$number % 10];
                }
            }
        }

        return trim($output);
    }
}
