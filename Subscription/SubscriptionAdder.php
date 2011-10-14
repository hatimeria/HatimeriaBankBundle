<?php

namespace Hatimeria\BankBundle\Subscription;

/**
 * Subscription adder
 *
 * @author Michal Wujas
 */
class SubscriptionAdder
{
    /**
     * Subscription class
     */
    private $class;
    /**
     * Entity manager
     */
    private $em;
    
    public function __construct($em, $modelPath)
    {
        $this->em    = $em;
        $this->class = $modelPath.'\Subscription';
    }
    
    public function add($service, $account)
    {
        $now = new \DateTime;
        
        $subscription = new $this->class;
        $subscription->setCode($service->getCode());
        $subscription->setValidTo($now->modify(sprintf("+ %d day", $service->getDuration())));
        $user = $account->getUser();
        
        $user->setSubscription($subscription);
        // @todo use configurable user manager instead
        $this->em->persist($user);
        $this->em->flush();
    }
}