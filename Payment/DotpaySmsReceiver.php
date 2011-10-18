<?php

namespace Hatimeria\BankBundle\Payment;

use Hatimeria\DotpayBundle\Request\PremiumTc;
use Hatimeria\BankBundle\Bank\Transaction;

/**
 * Dotpay message receiver
 *
 * @author michal
 */
class DotpaySmsReceiver
{
    /**
     * SmsManager
     */
    private $sm;
    
    public function __construct($sm)
    {
        $this->sm = $sm;
    }

    public function execute(Event $event)
    {
        /* @var \Hatimeria\DotpayBundle\Request\PremiumTc $request */
        $request = $event->getSubject();

        if (!($request instanceof PremiumTc)) {
            return;
        }

        $payment = $this->sm->createSmsPayment();
        $payment->setCode($request->code);
        $payment->setAmount($this->configuration[$request->number]);

        $this->sm->updateSmsPayment($payment);

        $event->setResult(true);
    }

    public function validate(ValidationEvent $event)
    {
        /* @var \Hatimeria\DotpayBundle\Request\PremiumTc $request */
        $request = $event->getSubject();

        if (!($request instanceof PremiumTc)) {
            return;
        }

        $code   = $request->code;
        $number = $request->number;

        // checkin if code is not already in our database [unique]
        if (is_object($this->sm->findSmsPaymentByCode($code))) {
            $event->markAsInvalid();

            return;
        }

        // checking the number where sms was sent
        if (array_key_exists($number, $this->configuration)) {
            $event->markAsValid();

            return;
        }
        $event->markAsInvalid();
    }

}
