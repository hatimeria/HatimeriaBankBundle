<?php

namespace Hatimeria\BankBundle\Bank;

use Hatimeria\BankBundle\Currency\CurrencyCode;
use Hatimeria\BankBundle\Model\Account;
use Hatimeria\BankBundle\Bank\BankException;

/**
 * MegaTotal Bank, handle currency conversion, megacents
 *
 * @author michal
 */
class Bank 
{
    /**
     * Exchanger
     *
     * @var CurrencyExchanger
     */
    private $exchanger;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    /**
     * @var \Hatimeria\BankBundle\Model\BankLogManager
     */
    private $blm;
    /**
     * Invoice Manager
     *
     * @var \Hatimeria\BankBundle\Model\InvoiceManger
     */
    private $im;
    
    public function __construct($exchanger, $em, $blm, $im)
    {
        $this->exchanger = $exchanger;
        $this->em        = $em;
        $this->blm       = $blm;
        $this->im        = $im;
    }

    protected function beforeTransaction(Transaction $transaction)
    {
        // @change me into event
        if($transaction->needExchange()) {
            $exchanged = $this->exchanger->exchange($transaction->getAmount(), $transaction->getCurrency(), CurrencyCode::VIRTUAL);
            $transaction->setVirtualAmount($exchanged);
        }
    }
    
    protected function afterTransaction(Transaction $transaction)
    {
        // @change me into event
        $this->em->persist($transaction->getAccount());

        if($transaction->isLogginEnabled()) {
            $this->blm->persistTransactionLog($transaction);
        }
        
        if($transaction->isInvoiceEnabled()) {
            $this->im->createFromTransaction($transaction);
        }
    }
    
    public function deposit(Transaction $transaction)
    {
        $this->beforeTransaction($transaction);
        
        $account = $transaction->getAccount();
        $account->addFunds($transaction->getVirtualAmount());
        
        $this->afterTransaction($transaction);
    }    
    
    public function withdraw(Transaction $transaction)
    {
        $this->beforeTransaction($transaction);
        $account = $transaction->getAccount();
        
        if($transaction->getAmount() > $account->getBalance()) {
            // @todo add more debug information
            throw new NotEnoughFundException();
        }
        
        $account->removeFunds($transaction->getAmount());
        $this->afterTransaction($transaction);
    }

    // @todo move round to int to rounding service
    public function roundToInt($amount)
    {
        return round($amount, 0, PHP_ROUND_HALF_DOWN);
    }
    
    public function transferFrozenFunds(Account $original, Account $destination, $amount)
    {
        if($original->getFrozen() < $amount) {
            throw new BankException(sprintf("Not enough FROZEN founds %d < %d", $original->getFrozen(), $amount));
        }
        
        $amount = $this->roundToInt($amount);
        $original->removeFrozen($amount);
        $destination->addFunds($amount);
        $this->em->persist($original);
        $this->em->persist($destination);
    }
    
    public function transferFunds(Account $original, Account $destination, $amount)
    {
        // @todo change into dual transaction - withdraw and deposit
        if($original->getBalance() < $amount) {
            throw new NotEnoughFundException($message, $code, $previous);
        }
        
        $amount = $this->roundToInt($amount);
        $original->removeFunds($amount);
        $destination->addFunds($amount);
        $this->em->persist($original);
        $this->em->persist($destination);
    }
    
    public function freezeFunds(Account $account, $amount)
    {
        if($account->getBalance() < $amount) {
            throw new BankException("Not enough founds");
        }
        
        $account->freeze($amount);
        $this->em->persist($account);
    }
    
    public function getExchanger()
    {
        return $this->exchanger;
    }
    
    public function getHistory()
    {
        throw new BankException("Not yet implemented");
    }

    public function getDepositHistoryQuery(Account $account)
    {
        $qb = $this->blm->getRepository()->createQueryBuilder('e');
        $qb->andWhere('e.account = '. $account->getId());

        return $qb;
    }

}