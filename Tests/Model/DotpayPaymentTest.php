<?php

namespace Hatimeria\BankBundle\Tests\Model;

use Hatimeria\FrameworkBundle\Test\TestCase;

use Hatimeria\BankBundle\Tests\TestEntity\DotpayPayment;
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
        $dp = new DotpayPayment();
        $dp->setId(5);

        $this->assertEquals(5, $dp->getId());
    }

    public function testAmount()
    {
        $dp = new DotpayPayment();
        $dp->setAmount(15);

        $this->assertEquals(15, $dp->getAmount());
    }

    public function testStatus()
    {
        $dp = new DotpayPayment();
        $dp->setStatus(DotpayPaymentStatus::NONE);

        $this->assertEquals(DotpayPaymentStatus::NONE, $dp->getStatus());
    }

    public function testIsFinished()
    {
        $dp = new DotpayPayment();
        $dp->setStatus(DotpayPaymentStatus::NONE);

        $this->assertFalse($dp->isFinished());

        $dp->setStatus(DotpayPaymentStatus::FINISHED);

        $this->assertTrue($dp->isFinished());
    }

    public function testIsCharge()
    {
        $dp = new DotpayPayment();
        $dp->setIsCharge(true);

        $this->assertTrue($dp->getIsCharge());

        $dp->setIsCharge(false);

        $this->assertFalse($dp->getIsCharge());
    }

}