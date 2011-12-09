<?php

namespace Hatimeria\BankBundle\Model;

use Doctrine\ORM\EntityManager;

use Hatimeria\BankBundle\Model\Account;
use Hatimeria\BankBundle\Bank\Bank;
use Hatimeria\BankBundle\Currency\CurrencyCode;
use Hatimeria\BankBundle\Model\Enum\DotpayPaymentStatus;
use Hatimeria\BankBundle\Bank\Transaction;
use Hatimeria\BankBundle\Bank\BankException;

use Hatimeria\DotpayBundle\Event\ValidationEvent;
use Hatimeria\DotpayBundle\Event\Event;
use Hatimeria\DotpayBundle\Response\Response as DotpayResponse;
use Hatimeria\DotpayBundle\Request\PremiumTc;

/**
 * Dotpay Sms Manager
 *
 * @author Michal Wujas
 */
class SmsManager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;
    /**
     * @var \Hatimeria\BankBundle\Bank\Bank
     */
    protected $bank;
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $repository;
    /**
     * @var array
     */
    protected $configuration;
    /**
     * SmsPayment class path
     *
     * @var string
     */
    protected $class;

    public function __construct(EntityManager $em, Bank $bank, $modelPath, $configuration)
    {
        $this->em               = $em;
        $this->bank             = $bank;
        $this->class            = $modelPath.'\SmsPayment';
        $this->configuration    = $configuration;
    }
    
    public function getRepository()
    {
        if($this->repository == null) {
            $this->repository    = $this->em->getRepository($this->class);
        }
        
        return $this->repository;
    }
    
    /**
     * @return SmsPayment
     */
    public function createSmsPayment()
    {
        $sms = new $this->class;

        return $sms;
    }

    public function updateSmsPayment(SmsPayment $payment)
    {
        $this->em->persist($payment);
        $this->em->flush();
    }

    /**
     * @param string $code
     * @return \Hatimeria\BankBundle\Model\SmsPayment
     */
    public function findSmsPaymentByCode($code)
    {
        return $this->getRepository()->findOneBy(array('code' => $code));
    }

    public function finishSmsPayment(Account $account, SmsPayment $payment)
    {
        if ($payment->isFinished()) {
            return;
        }

        $transaction = new Transaction($account);
        $transaction->setAmount($payment->getAmount());
        $transaction->setCurrency(CurrencyCode::VIRTUAL);
        $transaction->setInformation('Doładowanie poprzez sms');

        $this->bank->deposit($transaction);

        $payment->setStatus(DotpayPaymentStatus::FINISHED);
        $this->updateSmsPayment($payment);
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

}