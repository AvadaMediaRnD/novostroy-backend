<?php

namespace common\helpers;

class PriceHelper {
    
    public static function format($value, $withDecimals = true, $withCurrency = false, $thousandsSymbol = ' ', $currencyPrefix = '', $currencySuffix = ' грн')
    {
        $valueFormat = number_format($value, ($withDecimals ? 2 : 0), '.', $thousandsSymbol);
        if ($withCurrency && ($currencyPrefix || $currencySuffix)) {
            $valueFormat = $currencyPrefix . $valueFormat . $currencySuffix;
        }
        return $valueFormat;
    }
    
    public static function rateText($value)
    {
        $value = round($value, 2);
        $fullPart = floor($value);
        $decimalPart = $value - $fullPart;
        $decimalHundredPart = $decimalPart * 100;
        
        return "$fullPart гривень $decimalHundredPart копійок";
    }
    
    public static $_1_2 = [1 => "одна ", "дві "];
    public static $_1_19 = [
        1 => "один ", "два ", "три ", "чотири ", "п'ять ", "шість ", "сім ", "вісім ", "дев'ять ", "десять ",
        "одиндцать ", "дванадцять ", "тринадцять ", "чотирнадцять ", "п'ятнадцять ", "шістнадцять ", "сімнадцять ", "вісімнадцять ", "дев'ятнадцять ", 
    ];
    public static $des = [2 => "двадцять ", "тридцять ", "сорок ", "п'ятдесят ", "шістдесят ", "сімдесят ", "вісімдесят ", "дев'яносто "];
    public static $hang = [1 => "сто ", "двісті ", "триста ", "чотириста ", "п'ятсот ", "шістсот ", "сімсот ", "вісімсот ", "дев'ятсот "];
    public static $nameuah = [1 => "гривня ", "гривні ", "гривень "];
    public static $nameusd = [1 => "доллар США ", "доллара США ", "долларів США "];
    public static $nametho = [1 => "тисяча ", "тисячі ", "тисяч "];
    public static $namemil = [1 => "мильйон ", "мильйона ", "мильйонів "];
    public static $namemrd = [1 => "мильярд ", "мильярда ", "мильярдів "];
    public static $kopeekuah = [1 => "копійка ", "копійки ", "копійок "];
    public static $kopeekusd = [1 => "цент ", "цента ", "центів "];
    public static $nameempty = [1 => " ", " ", " "];
    public static $kopeekempty = [1 => " ", " ", " "];
    
    public static function text($L, $currencyCode = 'UAH', $includeCurrencyLabel = true) 
    {
        $namecurr = ($currencyCode == 'UAH' ? static::$nameuah : static::$nameusd);
        $kopeekcurr = ($currencyCode == 'UAH' ? static::$kopeekuah : static::$kopeekusd);
        
        if (!$includeCurrencyLabel) {
            $namecurr = static::$nameempty;
            $kopeekcurr = static::$kopeekempty;
        }
        
        $L = round($L, 2);
        $LInit = $L;
        
        $s = " ";
        $s1 = " ";
        $s2 = " ";
        $kop = intval(( $L * 100 - intval($L) * 100));
        $L = intval($L);
        if ($L >= 1000000000) {
            $many = 0;
            static::semantic(intval($L / 1000000000), $s1, $many, 3);
            $s .= $s1 . static::$namemrd[$many];
            $L %= 1000000000;
        }

        if ($L >= 1000000) {
            $many = 0;
            static::semantic(intval($L / 1000000), $s1, $many, 2);
            $s .= $s1 . static::$namemil[$many];
            $L %= 1000000;
            if ($L == 0) {
                $s .= $namecurr[3] . ' ';
            }
        }

        if ($L >= 1000) {
            $many = 0;
            static::semantic(intval($L / 1000), $s1, $many, 1);
            $s .= $s1 . static::$nametho[$many];
            $L %= 1000;
            if ($L == 0) {
                $s .= $namecurr[3] . ' ';
            }
        }

        if ($L != 0) {
            $many = 0;
            static::semantic($L, $s1, $many, 0);
            $s .= $s1 . $namecurr[$many];
        }

        if ($kop > 0) {
//            $many = 0;
//            static::semantic($kop, $s1, $many, 1);
//            $s .= $s1 . $kopeekcurr[$many];
            $many = 0;
            static::semantic($kop, $s1, $many, 1);
            
            if (!$includeCurrencyLabel) {
                $s .= ", ";
            }
            
            $s .= "$kop " . $kopeekcurr[$many];
        }/* else {
            $s .= " 00 копійок";
        }*/

        return "$LInit ($s)";
    }
    
    private static function semantic($i, &$words, &$fem, $f) 
    {
        $words = "";
        $fl = 0;
        if ($i >= 100) {
            $jkl = intval($i / 100);
            $words .= static::$hang[$jkl];
            $i %= 100;
        }
        if ($i >= 20) {
            $jkl = intval($i / 10);
            $words .= static::$des[$jkl];
            $i %= 10;
            $fl = 1;
        }
        switch ($i) {
            case 1: $fem = 1;
                break;
            case 2:
            case 3:
            case 4: $fem = 2;
                break;
            default: $fem = 3;
                break;
        }
        if ($i) {
            if ($i < 3 && $f > 0) {
                if ($f >= 2) {
                    $words .= static::$_1_19[$i];
                } else {
                    $words .= static::$_1_2[$i];
                }
            } else {
                $words .= static::$_1_19[$i];
            }
        }
    }

}
