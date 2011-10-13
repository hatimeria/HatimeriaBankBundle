<?php

namespace Hatimeria\BankBundle\Bank;

use Hatimeria\BankBundle\Model\Account;

class Transaction
{
    /**
     * @var \Hatimeria\BankBundle\Model\Account
     */
    protected $account;

    protected $amount;
    /**
     * @var string
     */
    protected $currency;

    protected $type;

    protected $information;

    public function __construct(Account $account, $amount = null, $currency = null)
    {
        $this->account  = $account;
        $this->amount   = $amount;
        $this->currency = $currency;
    }

    /**
     * @return \Hatimeria\BankBundle\Model\Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($v)
    {
        $this->amount = $v;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    public function getInformation()
    {
        return $this->information;
    }

    public function setInformation($information)
    {
        $this->information = $information;
    }

    public function hasInformation()
    {
        return null !== $this->information;
    }

}
