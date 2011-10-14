<?php

namespace Hatimeria\BankBundle\Service;

use Hatimeria\BankBundle\Bank\BankException;
use Hatimeria\BankBundle\Subscription\Subscription;
use Hatimeria\BankBundle\Currency\VirtualPackage;

/**
 * Finds subscription or virtual package
 *
 * @author Michal Wujas
 */
class Finder
{
    private $subscriptions, $packages;
    
    public function __construct($subscriptions, $packages)
    {
        $this->subscriptions = $subscriptions;
        $this->packages     = $packages;
    }
    
    public function code($code)
    {
        $parts = explode('_',$code);
        $formatException = new BankException("Invalid service code name");
        $type = array_shift($parts);
        $subtype = array_shift($parts);
        
        switch ($type) {
            case 'subscription':
                $config = $this->subscriptions->getConfig();
                $variant = array_shift($parts);
                if(!isset($config[$subtype]['variants'][$variant])) {
                    throw $formatException;
                }
                
                $service = new Subscription($config[$subtype]['variants'][$variant], $config[$subtype]['name']);
                
                break;
            case 'credits':
                $config = $this->packages->getConfig();
                if(!isset($config[$subtype])) {
                    throw $formatException;
                }
                
                $service = new VirtualPackage($config[$subtype]);
                break;
            default:
                throw $formatException;
        }
        
        $service->setCode($code);
        
        return $service;
    }    
}