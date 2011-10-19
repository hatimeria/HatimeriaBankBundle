<?php

namespace Hatimeria\BankBundle\Currency;

/**
 * Virtual currency manager
 *
 * @author Michal Wujas
 */
class VirtualManager
{
    private $config;
    
    public function __construct($config)
    {
        $this->config = $config;
        foreach($this->config as &$variant) {
            $costWithTax = (1 + $this->getTax())*$variant['cost'];
            $variant['cost_without_tax'] = $variant['cost'];
            $variant['cost'] = $costWithTax;
        }        
    }
    
    public function getConfig()
    {
        return $this->config;
    }
    
    public function getTax()
    {
        return 0.23;
    }
}