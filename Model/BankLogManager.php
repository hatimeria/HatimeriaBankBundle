<?php

namespace Hatimeria\BankBundle\Model;

use Doctrine\ORM\EntityManager;

use Hatimeria\BankBundle\Bank\Transaction;

class BankLogManager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $repository;
    /**
     * Entity class for bank log
     *
     * @var string
     */
    protected $class;

    public function __construct(EntityManager $em, $modelPath)
    {
        $this->em = $em;
        $this->class = $modelPath.'\BankLog';
        $this->repository = $em->getRepository($this->class);
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    public function createLog()
    {
        return new $this->class;
    }
    
    public function createTransactionLog(Transaction $transaction)
    {
        $log = $this->createLog();
        $log->setAccount($transaction->getAccount());
        $log->setAmount($transaction->getVirtualAmount());
        $log->setInformation($transaction->getInformation());
        
        $transaction->setLog($log);
        
        return $log;
    }
    
    public function persistTransactionLog(Transaction $transaction)
    {
        $log = $this->createTransactionLog($transaction);
        $this->updateLog($log);
    }

    public function updateLog(BankLog $log)
    {
        $this->em->persist($log);
    }

    public function findByAccount(Account $account)
    {
        return $this->repository->findBy(array('account' => $account->getId()));
    }

}