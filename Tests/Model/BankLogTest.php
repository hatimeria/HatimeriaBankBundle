<?php

namespace Hatimeria\BankBundle\Tests\Model;

use Hatimeria\FrameworkBundle\Test\TestCase;

use Hatimeria\BankBundle\Tests\TestEntity\BankLog;

/**
 * This test is only other tests complement to pass code coverage
 *
 * Most important tests:
 * * testToStoreArray()
 */
class BankLogTest extends TestCase
{
    public function testId()
    {
        $log = new BankLog();
        $log->setId(5);

        $this->assertEquals(5, $log->getId());
    }

    public function testToStoreArray()
    {
        $date = new \DateTime();

        $log = new BankLog();
        $log->setInformation('test');
        $log->setAmount(1000);

        $assert = array(
            'amount'     => 1000,
            'created_at' => $date,
            'desc'       => 'test'
        );

        $this->assertEquals($assert, $log->toStoreArray());
    }

}