<?php

namespace Hatimeria\BankBundle\Bank;

use Hatimeria\BankBundle\Bank\CurrencyExchanger;
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

    public function __construct($exchanger, $em, $blm)
    {
        $this->exchanger = $exchanger;
        $this->em        = $em;
        $this->blm       = $blm;
    }
    
    public function addAmountToAccount($amount, $currency, Account $account)
    {
        $cents = $this->exchanger->exchange($amount, $currency, CurrencyExchanger::MC);
        $account->addFunds($cents);
        $this->em->persist($account);
    }

    public function deposit(Transaction $transaction)
    {
        $cents = $this->exchanger->exchange($transaction->getAmount(), $transaction->getCurrency(), CurrencyExchanger::MC);
        $account = $transaction->getAccount();
        $account->addFunds($cents);
        $this->em->persist($account);

        if ($transaction->hasInformation()) {
            $log = $this->blm->createLog($transaction);
            $this->blm->updateLog($log);
        }
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
        if($original->getBalance() < $amount) {
            throw new BankException("Not enough founds");
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