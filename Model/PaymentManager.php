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
    protected $dotpayRepository;
    /**
     * @var array
     */
    protected $smsConfiguration;
    /**
     * DotpayPayment class path
     *
     * @var string
     */
    protected $dotpayClass;
    /**
     * SmsPayment class path
     *
     * @var string
     */
    protected $smsClass;

    public function __construct(EntityManager $em, Bank $bank, $modelPath)
    {
        $this->em               = $em;
        $this->bank             = $bank;
        $this->dotpayClass      = $modelPath.'\DotpayPayment';
        $this->smsClass         = $modelPath.'\SmsPayment';
        $this->smsConfiguration = array(
            '71068' => 10,
            '72068' => 20,
            '73068' => 30,
            '75068' => 50,
            '79068' => 90
        );
    }
    
    public function getSmsRepository()
    {
        if($this->smsRepository == null) {
            $this->smsRepository    = $this->em->getRepository($this->smsClass);
        }
        
        return $this->smsRepository;
    }
    
    public function getDotpayRepository()
    {
        if($this->dotpayRepository == null) {
            $this->dotpayRepository = $this->em->getRepository($this->dotpayClass);        
        }
        
        return $this->dotpayRepository;
    }

    public function createDotpayPayment(Account $account)
    {
        $payment = new $this->dotpayClass;
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
     * @return SmsPayment
     */
    public function createSmsPayment()
    {
        $payment = new $this->smsClass;

        return $payment;
    }

    public function updateSmsPayment(SmsPayment $payment)
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
        return $this->getDotpayRepository()->findOneBy(array('control' => $control));
    }

    /**
     * @param string $code
     * @return \Hatimeria\BankBundle\Model\SmsPayment
     */
    public function findSmsPaymentByCode($code)
    {
        return $this->getSmsRepository()->findOneBy(array('code' => $code));
    }

    public function validateDotpayPayment(ValidationEvent $event)
    {
        /* @var \Hatimeria\DotpayBundle\Response\Response $response */
        $response = $event->getSubject();

        if (!($response instanceof DotpayResponse)) {
            return;
        }

        $dotpayPayment = $this->findDotpayPaymentByControl($response->getControl());

        if (is_object($dotpayPayment)) {
            $event->markAsValid();

            return;
        }

        $event->markAsInvalid();
    }

   public function executeDotpayResponse(Event $event)
    {
        /* @var \Hatimeria\DotpayBundle\Response\Response $response */
        $response = $event->getSubject();

        if (!($response instanceof DotpayResponse)) {
            return;
        }

        $dotpayPayment = $this->findDotpayPaymentByControl($response->getControl());
        
        if ($payment->isFinished()) {
            return $event->setResult(false);
        }
        if (!$response->isStatusMade()) {
            //@todo Add code for other status
            throw new \Exception('Only status MADE is implemented :/');
        }
        
        //@todo save transcation id
        
        $this->executeDotpayPayment($payment);
        
        $event->setResult(true);
    }

    public function executeDotpayPayment($payment)
    {
        $transaction = new Transaction($payment->getAccount());
        $transaction->setAmount($payment->getAmount());
        // @todo save currency in dotpay payment
        $transaction->setCurrency(CurrencyCode::PLN);
        $transaction->setInformation('Doładowanie poprzez dotpay ');

        if($payment->isCharge()) {
            $this->bank->deposit($transaction);
        } else {
            // @todo handle subscriptions adding to user
        }
        
        $payment->setStatus(DotpayPaymentStatus::FINISHED);
        $this->updateDotpayPayment($payment);
    }

    public function validateSmsPayment(ValidationEvent $event)
    {
        /* @var \Hatimeria\DotpayBundle\Request\PremiumTc $request */
        $request = $event->getSubject();

        if (!($request instanceof PremiumTc)) {
            return;
        }

        $code   = $request->code;
        $number = $request->number;

        // checkin if code is not already in our database [unique]
        if (is_object($this->findSmsPaymentByCode($code))) {
            $event->markAsInvalid();

            return;
        }

        // checking the number where sms was sent
        if (array_key_exists($number, $this->smsConfiguration)) {
            $event->markAsValid();

            return;
        }
        $event->markAsInvalid();
    }

    public function executeSmsPayment(Event $event)
    {
        /* @var \Hatimeria\DotpayBundle\Request\PremiumTc $request */
        $request = $event->getSubject();

        if (!($request instanceof PremiumTc)) {
            return;
        }

        $payment = $this->createSmsPayment();
        $payment->setCode($request->code);
        $payment->setAmount($this->smsConfiguration[$request->number]);

        $this->updateSmsPayment($payment);

        $event->setResult(true);
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
    
    public function createFromService($account, $service)
    {
        $payment   = $this->createDotpayPayment($account);
        $payment->setAmount($service->getCost());
        $this->updateDotpayPayment($payment);
        
        return $payment;
    }
}