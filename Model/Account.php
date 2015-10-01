<?php

namespace Hatimeria\BankBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Hatimeria\BankBundle\Bank\Bank;
use Hatimeria\BankBundle\Decimal\Decimal;

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
     * @ORM\Column(type="decimal", precision=60, scale=14)
     *
     * @var float
     */
    protected $balance;
    /**
     * @ORM\Column(type="decimal", precision=60, scale=14)
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
        if (!$amount instanceof Decimal) {
            $amount = new Decimal($amount);
        }

        $this->balance = Decimal::create($this->balance)->sub($amount)->getAmount();
    }
    
    public function addFunds($amount)
    {
        if (!$amount instanceof Decimal) {
            $amount = new Decimal($amount);
        }
        
        $this->balance = Decimal::create($this->balance)->add($amount)->getAmount();
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
        if (!$amount instanceof Decimal) {
            $amount = new Decimal($amount);
        }

        $this->balance = Decimal::create($this->balance)->sub($amount)->getAmount();
        $this->frozen  = Decimal::create($this->frozen)->add($amount)->getAmount();
    }
    
    public function getFrozen()
    {
        return $this->frozen;
    }
    
    public function removeFrozen($amount)
    {
        if (!$amount instanceof Decimal) {
            $amount = new Decimal($amount);
        }

        $this->frozen = Decimal::create($this->frozen)->sub($amount)->getAmount();
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
        $this->subscriptionDiscount = (float)$v;
    }
    
    public function hasFullSubscriptionDiscount()
    {
        return $this->getSubscriptionDiscount() === 100.00;
    }

}
