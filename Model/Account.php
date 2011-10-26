<?php

namespace Hatimeria\BankBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @author michal
 */
abstract class Account
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(type="float")
     *
     * @var float
     */
    protected $balance;
    /**
     * @ORM\Column(type="float")
     * 
     * @var float
     */
    protected $frozen;
    /**
     * Percent number eq 10.5 = 10.5%
     * @ORM\Column(type="float", name="subscription_discount")
     *
     * @var float
     */
    protected $subscriptionDiscount;
    
    public function __construct()
    {
        $this->balance = 0;
        $this->frozen = 0;
        $this->subscriptionDiscount = 0;
    }
    
    public function getBalance()
    {
        return $this->balance;
    }
    
    public function removeFunds($amount)
    {
        $this->balance -= $amount;
    }
    
    public function addFunds($amount)
    {
        $this->balance += $amount;
    }
    
    public function setBalance($balance)
    {
        $this->balance = $balance;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function freeze($amount)
    {
        $this->balance -= $amount;
        $this->frozen += $amount;
    }
    
    public function getFrozen()
    {
        return $this->frozen;
    }
    
    public function removeFrozen($amount)
    {
        $this->frozen -= $amount;
    }
    
    public function hasSubscriptionDiscount()
    {
        return $this->subscriptionDiscount !== 0;
    }
    
    public function getSubscriptionDiscount()
    {
        return $this->subscriptionDiscount;
    }
    
    public function setSubscriptionDiscount($v)
    {
        $this->subscriptionDiscount = $v;
    }
    
    public function hasFullSubscriptionDiscount()
    {
        return $this->getSubscriptionDiscount() === 100.00;
    }
}