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
    }
    
    public function getConfig()
    {
        return $this->config;
    }
}