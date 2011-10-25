<?php

namespace Hatimeria\BankBundle\Currency;

use Hatimeria\BankBundle\Service\Manager;

/**
 * Virtual currency manager
 *
 * @author Michal Wujas
 */
class VirtualManager extends Manager
{
    public function initalize()
    {
        foreach($this->config as &$variant) {
            $variant = $this->calculatePrice($variant);
        }
    }
}