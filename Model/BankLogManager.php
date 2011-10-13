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

    public function createLog(Transaction $transaction = null)
    {
        $log = new $this->class;
        
        if (null !== $transaction) {
            $log->setAccount($transaction->getAccount());
            $log->setAmount($transaction->getAmount());
            $log->setInformation($transaction->getInformation());
        }

        return $log;
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