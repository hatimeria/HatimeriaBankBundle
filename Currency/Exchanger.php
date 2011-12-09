<?php

namespace Hatimeria\BankBundle\Currency;

use Hatimeria\BankBundle\Decimal\Decimal;

/**
 * Currency exchanger
 * 
 * supported currencies: PLN, Virtual
 *
 * @author michal
 */
class Exchanger
{
    // pln to virtual ratio
    private $ratio;

    public function __construct($ratio)
    {
        $this->ratio = $ratio;
    }

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

        $amount = new Decimal($amount);

        if($originalCurrency == CurrencyCode::PLN) {
            return $amount->mul($this->ratio)->getAmount();
        } else {
            return $amount/$this->ratio;
        }
    }
}