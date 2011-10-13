<?php

namespace Hatimeria\BankBundle\Currency;

/**
 * Currency exchanger
 * 
 * supported currencies: PLN, MC - megacents
 *
 * @author michal
 */
class Exchanger
{
    // pln to virtual ratio
    private $ratio = 10;
    
    /**
     * Get exchanged amount
     *
     * @param int $amount
     * @param string $originalCurrency
     * @param string $destinationCurrency 
     */
    public function exchange($amount, $originalCurrency, $destinationCurrency = null)
    {
        if (null !== $destinationCurrency) {
            if ($originalCurrency == $destinationCurrency) {
                return $amount;
            }
        }
        
        if($originalCurrency == CurrencyCode::PLN) {
            return $amount*$this->ratio;
        } else {
            return $amount/$this->ratio;
        }
    }
}