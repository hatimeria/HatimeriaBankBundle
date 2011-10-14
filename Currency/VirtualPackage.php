<?php

namespace Hatimeria\BankBundle\Currency;

use Hatimeria\BankBundle\Service\Service;

/**
 * Virtual Currency Package
 *
 * @author Michal Wujas
 */
class VirtualPackage extends Service
{
    private $amount, $cost;
    
    public function __construct($config)
    {
        $this->amount = $config['amount'];
        $this->cost     = $config['cost'];
    }
    
    public function getDescription()
    {
        return sprintf('Pakiet %d punktów', $this->amount);
    }

    public function getCost()
    {
        return $this->cost;
    }
    
    public function getAmount()
    {
        return $this->amount;
    }
}