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
    }
    
    public function getConfig()
    {
        return $this->config;
    }
}