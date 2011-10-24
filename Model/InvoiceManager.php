<?php

namespace Hatimeria\BankBundle\Model;

use Doctrine\ORM\EntityManager;

use Hatimeria\BankBundle\Bank\Transaction;

class InvoiceManager
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
        $this->class = $modelPath.'\Invoice';
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
    
    public function createFromTransaction(Transaction $transaction)
    {
        if(!$transaction->getAccount()->getUser()->isAccountable()) {
            return;
        }
        
        $invoice = new $this->class;
        $invoice->setAccount($transaction->getAccount());
        $invoice->setAmount($transaction->getAmount());
        $invoice->setTitle($transaction->getInformation());
        $invoice->generateNumber($this, $transaction);
        
        $this->em->persist($invoice);
    }
    
    public function create()
    {
        return new $this->class;
    }
    
    public function findLastInvoiceNumber()
    {
        // @todo not working if there's more than one invoice in current request
        $qb = $this->repository->createQueryBuilder("i");
        $qb->andWhere("i.year = ".date('Y'));
        $qb->andWhere("i.month = ".date('m'));
        $qb->select('max(i.number)');
        $number = $qb->getQuery()->getOneOrNullResult();
        
        if($number) {
            return $number[1];
        } else {
            return 0;
        }
    }

    public function findOneById($id)
    {
        return $this->repository->findOneBy(array("id" => $id));
    }    
    
    public function getQueryForUser($user)
    {
        $qb = $this->repository->createQueryBuilder("i");
        $qb->andWhere("i.account = ".$user->getAccount()->getId());
        
        return $qb;
    }
}