<?php

namespace App\Helpers;

use NumberToWords\NumberToWords;

class NumberToWordsHelper
{
    public static function toWords($number, $lang = 'fr')
    {
        $numberToWords = new NumberToWords();
        $transformer = $numberToWords->getNumberTransformer($lang);
        
        return $transformer->toWords($number);
    }

    public static function toOrdinal($number, $lang = 'fr')
    {
        $numberToWords = new NumberToWords();
        $transformer = $numberToWords->getNumberTransformer($lang);
        
        return $transformer->toOrdinal($number);
    }

    public static function toWordsWithDecimals($number, $lang = 'fr')
    {
        $numberToWords = new NumberToWords();
        $transformer = $numberToWords->getNumberTransformer($lang);
        
        if (strpos($number, '.') !== false) {
            [$integerPart, $decimalPart] = explode('.', $number);
            return $transformer->toWords((int)$integerPart) . ' virgule ' . $transformer->toWords((int)$decimalPart);
        }
        
        return $transformer->toWords($number);
    }
}