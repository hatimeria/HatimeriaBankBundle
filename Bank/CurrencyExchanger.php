<?php

namespace Hatimeria\BankBundle\Bank;

/**
 * Currency exchanger
 * 
 * supported currencies: PLN, MC - megacents
 *
 * @author michal
 */
class CurrencyExchanger
{
    const MC = 'MC';
    const PLN = 'PLN';
    
    // pln to mc ratio
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
        // @todo currency codes constant
        if($originalCurrency == CurrencyExchanger::PLN) {
            return $amount*$this->ratio;
        } else {
            return $amount/$this->ratio;
        }
        
    }
}