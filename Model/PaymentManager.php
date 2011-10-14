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
use Hatimeria\DotpayBundle\Request\PremiumTc;
use Hatimeria\BankBundle\Currency\VirtualPackage;
use Hatimeria\BankBundle\Subscription\Subscription as SubscriptionService;

class PaymentManager
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
     * DotpayPayment class path
     *
     * @var string
     */
    protected $class;
    /**
     * Service Finder
     */
    protected $finder;
    /**
     * Subscription adder
     */
    protected $sa;

    public function __construct(EntityManager $em, Bank $bank, $modelPath, $finder, $sa)
    {
        $this->em               = $em;
        $this->bank             = $bank;
        $this->class            = $modelPath.'\DotpayPayment';
        $this->finder           = $finder;
        $this->sa               = $sa;
    }
    
    public function getRepository()
    {
        if($this->repository == null) {
            $this->repository = $this->em->getRepository($this->class);        
        }
        
        return $this->repository;
    }

    public function createDotpayPayment(Account $account)
    {
        $payment = new $this->class;
        $payment->setAccount($account);
        $payment->setControl(md5($account->getId() . time()));

        return $payment;
    }

    public function updateDotpayPayment(DotpayPayment $payment)
    {
        $this->em->persist($payment);
        $this->em->flush();
    }

    /**
     * @param $control
     * @return \Hatimeria\BankBundle\Model\DotpayPayment
     */
    public function findDotpayPaymentByControl($control)
    {
        return $this->getRepository()->findOneBy(array('control' => $control));
    }

    public function executeDotpayPayment($payment)
    {
        $account = $payment->getAccount();

        if($payment->isCharge()) {
            $transaction = new Transaction();
            $transaction->setAmount($payment->getAmount());
            // @todo save currency in dotpay payment
            $transaction->setCurrency(CurrencyCode::PLN);
            $transaction->setInformation('Doładowanie poprzez dotpay ');
            $this->bank->deposit($transaction);
        } else {
            $service = $this->finder->code($payment->getService());
            $this->addServiceToAccount($service, $account);
        }
        
        $payment->setStatus(DotpayPaymentStatus::FINISHED);
        $this->updateDotpayPayment($payment);
    }

    public function createFromService($account, $service)
    {
        $payment   = $this->createDotpayPayment($account);
        $payment->setAmount($service->getCost());
        $payment->setService($service->getCode());
        $this->updateDotpayPayment($payment);
        
        return $payment;
    }
    
    private function addServiceToAccount($service, $account)
    {
        if($service instanceof VirtualPackage) {
            $this->bank->addAmountToAccount($service->getAmount(), CurrencyCode::VIRTUAL, $account);
        } elseif($service instanceof SubscriptionService) {
            $this->sa->add($service, $account);
        } else {
            throw new BankException("Unsupported service object class");
        }
    }
}