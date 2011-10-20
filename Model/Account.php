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
     * @var int
     */
    protected $balance;
    /**
     * @ORM\Column(type="float")
     * 
     * @var int
     */
    protected $frozen;
    
    public function __construct()
    {
        $this->balance = 0;
        $this->frozen = 0;
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
}