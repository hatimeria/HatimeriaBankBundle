<?php

namespace Hatimeria\BankBundle\Bank;

use Hatimeria\BankBundle\Model\Account;
use Hatimeria\BankBundle\Currency\CurrencyCode;

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
    
    protected $invoice;
    
    protected $logging;
    
    protected $virtualAmount;
    
    protected $log;
    
    protected $exchangeDisabled;

    protected $logType = 0;

    public function __construct(Account $account, $amount = null, $currency = CurrencyCode::VIRTUAL)
    {
        $this->account  = $account;
        $this->amount   = $amount;
        $this->currency = $currency;
        if($currency == CurrencyCode::VIRTUAL) {
            $this->virtualAmount = $amount;
        }
        $this->logging  = false;
        $this->invoice  = false;
        $this->exchangeDisabled = false;
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
    
    public function getVirtualAmount()
    {
        return $this->virtualAmount;
    }
    
    public function setVirtualAmount($v)
    {
        $this->virtualAmount = $v;
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
    
    public function enableLogging()
    {
        $this->logging = true;
    }
    
    public function disableLogging()
    {
        $this->logging = false;
    }
    
    public function enableInvoice()
    {
        $this->invoice = true;
    }
    
    public function disableInvoice()
    {
        $this->invoice = false;
    }    
    
    public function isLogginEnabled()
    {
        return $this->logging;
    }
    
    public function isInvoiceEnabled()
    {
        return $this->invoice;
    }
    
    public function isNotVirtualCurrency()
    {
        return $this->currency != CurrencyCode::VIRTUAL;
    }
    
    public function needExchange()
    {
        return $this->isNotVirtualCurrency() && !$this->exchangeDisabled;
    }
    
    public function disableExchange()
    {
        $this->exchangeDisabled = true;
    }
    
    public function setLog($log)
    {
        $this->log = $log;
    }
    
    public function getLog()
    {
        return $this->log;
    }

    public function hasLogType()
    {
        if($this->logType != 0)
        {
            return true;
        }else{
            return false;
        }
    }

    public function setLogType($v)
    {
        $this->logType = $v;
    }

    public function getLogType()
    {
        return $this->logType;
    }

}
