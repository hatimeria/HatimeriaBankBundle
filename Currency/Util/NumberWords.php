<?php

namespace Hatimeria\BankBundle\Currency\Util;

/**
 * NumberWords, replacement for Number_Words Pear class
 *
 * @author Michal Wujas
 */
class NumberWords
{
    /**
     * Locale Object
     */
    private $localeObject;
    /**
     * Default locale
     *
     * @var string
     */
    private $locale;
    
    public function __construct($locale)
    {
        $this->locale = $locale;
    }
    
    private function getLocaleObject()
    {
        if(is_null($this->localeObject)) {
            $localeFile = sprintf(dirname(__FILE__).'/locales/lang.%s.php', $this->locale);
            if(file_exists($localeFile)) {
                include_once $localeFile;
            }
            
            $class = "Numbers_Words_".$this->locale;
            
            $this->localeObject = new $class;
        }
        
        return $this->localeObject;
    }
    
    function toWords($num)
    {
        $obj = $this->getLocaleObject();

        if (!is_int($num)) {
            // cast (sanitize) to int without losing precision
            $num = preg_replace('/^[^\d]*?(-?)[ \t\n]*?(\d+)([^\d].*?)?$/', '$1$2', $num);
        }

        return trim($obj->toWords($num));
    }

    function toCurrency($num)
    {
        $ret = $num;
        $int_curr = '';
        
        $obj = $this->getLocaleObject();

        // round if a float is passed, use Math_BigInteger otherwise
        if (is_float($num)) {
            $num = round($num, 2);
        }

        if (strpos($num, '.') === false) {
            return trim($obj->toCurrencyWords($int_curr, $num));
        }

        $currency = explode('.', $num, 2);

        $len = strlen($currency[1]);

        if ($len == 1) {
            // add leading zero
            $currency[1] .= '0';
        } elseif ($len > 2) {
            // get the 3rd digit after the comma
            $round_digit = substr($currency[1], 2, 1);
            
            // cut everything after the 2nd digit
            $currency[1] = substr($currency[1], 0, 2);
            
            if ($round_digit >= 5) {
                // round up without losing precision
                include_once "Math/BigInteger.php";

                $int = new Math_BigInteger(join($currency));
                $int = $int->add(new Math_BigInteger(1));
                $int_str = $int->toString();

                $currency[0] = substr($int_str, 0, -2);
                $currency[1] = substr($int_str, -2);

                // check if the rounded decimal part became zero
                if ($currency[1] == '00') {
                    $currency[1] = false;
                }
            }
        }

        return trim($obj->toCurrencyWords($int_curr, $currency[0], $currency[1]));
    }    
}