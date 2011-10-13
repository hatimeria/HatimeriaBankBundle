<?php

namespace Hatimeria\BankBundle\Model;

use Doctrine\ORM\EntityManager;

use Hatimeria\BankBundle\Model\Account;
use Hatimeria\BankBundle\Bank\Bank;
use Hatimeria\BankBundle\Bank\CurrencyExchanger;
use Hatimeria\BankBundle\Model\Enum\DotpayPaymentStatus;
use Hatimeria\BankBundle\Bank\Transaction;

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
        $this->dotpayRepository = $em->getRepository($this->dotpayClass);
        $this->smsRepository    = $em->getRepository($this->smsClass);

        $this->smsConfiguration = array(
            '71068' => 10,
            '72068' => 20,
            '73068' => 30,
            '75068' => 50,
            '79068' => 90
        );
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
        return $this->dotpayRepository->findOneBy(array('control' => $control));
    }

    /**
     * @param string $code
     * @return \Hatimeria\BankBundle\Model\SmsPayment
     */
    public function findSmsPaymentByCode($code)
    {
        return $this->smsRepository->findOneBy(array('code' => $code));
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

    public function executeDotpayPayment(Event $event)
    {
        /* @var \Hatimeria\DotpayBundle\Response\Response $response */
        $response = $event->getSubject();

        if (!($response instanceof DotpayResponse)) {
            return;
        }

        $dotpayPayment = $this->findDotpayPaymentByControl($response->getControl());

        if ($dotpayPayment->isFinished()) {
            return $event->setResult(false);
        }
        if (!$response->isStatusMade()) {
            //@todo Add code for other status
            throw new \Exception('Only status MADE is implemented :/');
        }

        $transaction = new Transaction($dotpayPayment->getAccount());
        $transaction->setAmount($response->getAmount());
        $transaction->setCurrency(CurrencyExchanger::PLN);
        $transaction->setInformation('Doładowanie poprzez dotpay ' . $response->getTransactionId());

        $this->bank->deposit($transaction);
        
        $dotpayPayment->setStatus(DotpayPaymentStatus::FINISHED);
        $this->updateDotpayPayment($dotpayPayment);

        $event->setResult(true);
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
        $transaction->setCurrency(CurrencyExchanger::MC);
        $transaction->setInformation('Doładowanie poprzez sms');

        $this->bank->deposit($transaction);

        $payment->setStatus(DotpayPaymentStatus::FINISHED);
        $this->updateSmsPayment($payment);
    }

}