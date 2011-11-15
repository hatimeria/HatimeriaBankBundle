<?php

namespace Hatimeria\BankBundle\Service;

/**
 * Service Manager
 *
 * @author Michal Wujas
 */
abstract class Manager
{
    /**
     * Config
     *
     * @var array
     */
    protected $config;
    /**
     * If config has been parsed
     * 
     * @var bool
     */
    private $initialized = false;
    /**
     * Current context account
     *
     * @var Account
     */
    private $currentAccount;
    
    public function __construct($config, $security)
    {
        $this->config = $config;
        $this->security = $security;
    }
    
    public function getCurrentAccount()
    {
        if($this->currentAccount === null) {
            if($this->security->getToken() != null && $this->security->isGranted("ROLE_USER")) {
                $this->currentAccount = $this->security->getToken()->getUser()->getAccount();
            } else {
                $this->currentAccount = false;
            }
        }
        
        return $this->currentAccount;
    }
    
    abstract function initalize();
    
    public function getConfig()
    {
        if(!$this->initialized) {
            $this->initalize();
            $this->initialized = true;
        }
        
        return $this->config;
    }
    
    public function getTax()
    {
        return 0.23;
    }
    
    public function calculatePrice($service)
    {
        $account = $this->getCurrentAccount();
        
        if($account && $account->hasSubscriptionDiscount()) {
            $service['cost'] *= 1 - $account->getSubscriptionDiscount()/100; 
        }
        
        $costWithTax = (1 + $this->getTax())*$service['cost'];
        $service['cost_without_tax'] = $service['cost'];
        $service['cost'] = $costWithTax;
        
        return $service;
    }
}