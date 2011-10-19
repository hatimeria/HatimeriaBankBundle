<?php

namespace Hatimeria\BankBundle\Subscription;

use Hatimeria\BankBundle\Service\Service;

/**
 * Subscription
 *
 * @author Michal Wujas
 */
class Subscription extends Service
{
    private $duration, $cost, $type;
    
    public function __construct($config, $type)
    {
        $this->duration = $config['duration'];
        $this->cost     = $config['cost'];
        $this->costWithoutTax = $config['cost_without_tax'];
        $this->type     = $type;
    }
    
    public function getDescription()
    {
        return sprintf('Abonament %s na %d dni', $this->type, $this->duration);
    }

    public function getCost()
    {
        return $this->cost;
    }
    
    public function getDuration()
    {
        return $this->duration;
    }
}