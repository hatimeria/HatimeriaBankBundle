<?php

namespace Hatimeria\BankBundle\Subscription;

/**
 * Subscriptions
 *
 * @author Michal Wujas
 */
class SubscriptionManager
{
    private $config;
    
    public function __construct($config)
    {
        $this->config = $config;
        
        // @cleanup
        foreach($this->config as &$type) {
            foreach($type['variants'] as &$variant) {
                $costWithTax = (1 + $this->getTax())*$variant['cost'];
                $variant['cost_without_tax'] = $variant['cost'];
                $variant['cost'] = $costWithTax;
                if($variant['duration'] == 365) {
                    $variant['duration_description'] = 'Rok';
                } elseif($variant['duration'] == 182) {
                    $variant['duration_description'] = 'Pół roku';
                } else {
                    $variant['duration_description'] = $variant['duration'] . ' dni';
                }
            }
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