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
    /**
     * @var Hatimeria\BankBundle\Model\InvoiceManager
     */
    private $im;
    
    public function __construct($em, $modelPath, $im)
    {
        $this->em    = $em;
        $this->class = $modelPath.'\Subscription';
        $this->im    = $im;
    }
    
    public function add($service, $account)
    {
        $now = new \DateTime;
        
        $subscription = new $this->class;
        $subscription->setCode($service->getCode());
        $subscription->setValidTo($now->modify(sprintf("+ %d day", $service->getDuration())));
        $user = $account->getUser();
        
        $user->setSubscription($subscription);
        
        $invoice = $this->im->create();
        $invoice->setAccount($account);
        $invoice->setAmount($service->getCost());
        $invoice->setTitle($service->getDescription());
        $invoice->generateNumber($this->im);
        $this->em->persist($invoice);
        // @todo use configurable user manager instead
        $this->em->persist($user);
        $this->em->flush();
    }
}