<?php

namespace Hatimeria\BankBundle\Subscription;

/**
 * Subscription
 *
 * @author Michal Wujas
 */
class Subscription
{
    private $duration, $cost, $type;
    
    public function __construct($config, $type)
    {
        $this->duration = $config['duration'];
        $this->cost     = $config['cost'];
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
}