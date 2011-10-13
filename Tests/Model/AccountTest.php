<?php

namespace Hatimeria\BankBundle\Tests\Model;

use Hatimeria\FrameworkBundle\Test\TestCase;

use Hatimeria\BankBundle\Tests\TestEntity\Account;

/**
 * This test is only other tests complement to pass code coverage
 *
 * Most important (valuable) tests:
 * * testNewAccount()
 */
class AccountTest extends TestCase
{
    public function testNewAccount()
    {
        $account = new Account();

        // newly created Account object has balance equals 0
        $this->assertEquals(0, $account->getBalance());
    }

}