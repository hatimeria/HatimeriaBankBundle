<?php

namespace Hatimeria\BankBundle\Tests\Entity;

use Hatimeria\FrameworkBundle\Test\TestCase;

use Hatimeria\BankBundle\Tests\TestDotpayPayment;
use Hatimeria\BankBundle\Model\Enum\DotpayPaymentStatus;

/**
 * This test is only other tests complement to pass code coverage
 *
 * Most important tests:
 * * none ...
 */
class DotpayPaymentTest extends TestCase
{
    public function testId()
    {
        $dp = new TestDotpayPayment();
        $dp->setId(5);

        $this->assertEquals(5, $dp->getId());
    }

    public function testAmount()
    {
        $dp = new TestDotpayPayment();
        $dp->setAmount(15);

        $this->assertEquals(15, $dp->getAmount());
    }

    public function testStatus()
    {
        $dp = new TestDotpayPayment();
        $dp->setStatus(DotpayPaymentStatus::NONE);

        $this->assertEquals(DotpayPaymentStatus::NONE, $dp->getStatus());
    }

    public function testIsFinished()
    {
        $dp = new TestDotpayPayment();
        $dp->setStatus(DotpayPaymentStatus::NONE);

        $this->assertFalse($dp->isFinished());

        $dp->setStatus(DotpayPaymentStatus::FINISHED);

        $this->assertTrue($dp->isFinished());
    }

    public function testIsCharge()
    {
        $dp = new TestDotpayPayment();
        $dp->setIsCharge(true);

        $this->assertTrue($dp->getIsCharge());

        $dp->setIsCharge(false);

        $this->assertFalse($dp->getIsCharge());
    }

}