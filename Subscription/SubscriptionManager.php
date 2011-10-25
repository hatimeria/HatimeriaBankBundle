<?php

namespace Hatimeria\BankBundle\Subscription;

use Hatimeria\BankBundle\Service\Manager;

/**
 * Subscriptions
 *
 * @author Michal Wujas
 */
class SubscriptionManager extends Manager
{
    public function initalize()
    {
        // @cleanup translations
        foreach($this->config as &$type) {
            foreach($type['variants'] as &$variant) {
                
                $variant = $this->calculatePrice($variant);

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
}