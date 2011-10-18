<?php

namespace Hatimeria\BankBundle\Payment;

use Hatimeria\DotpayBundle\Event\ValidationEvent;
use Hatimeria\DotpayBundle\Event\Event;
use Hatimeria\DotpayBundle\Response\Response as DotpayResponse;

/**
 * Dotpay message receiver
 *
 * @author michal
 */
class DotpayTransferReceiver
{
    /**
     * PaymentManager
     *
     * @var type 
     */
    private $pm;
    
    public function __construct($pm)
    {
        $this->pm = $pm;
    }

    public function execute(Event $event)
    {
        /* @var \Hatimeria\DotpayBundle\Response\Response $response */
        $response = $event->getSubject();

        if (!($response instanceof DotpayResponse)) {
            return;
        }

        $dotpayPayment = $this->pm->findDotpayPaymentByControl($response->getControl());

        if ($payment->isFinished()) {
            return $event->setResult(false);
        }
        if (!$response->isStatusMade()) {
            //@todo Add code for other status
            throw new \Exception('Only status MADE is implemented :/');
        }

        //@todo save transcation id
        $this->pm->executeDotpayPayment($payment);

        $event->setResult(true);
    }

    public function validate(ValidationEvent $event)
    {
        /* @var \Hatimeria\DotpayBundle\Response\Response $response */
        $response = $event->getSubject();

        if (!($response instanceof DotpayResponse)) {
            return;
        }

        $payment = $this->pm->findDotpayPaymentByControl($response->getControl());

        if (is_object($payment)) {
            $event->markAsValid();

            return;
        }

        $event->markAsInvalid();
    }

}
