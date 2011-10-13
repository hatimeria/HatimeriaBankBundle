<?php

namespace Hatimeria\BankBundle\Tests\Entity;

use Hatimeria\FrameworkBundle\Test\TestCase;

use Hatimeria\BankBundle\Tests\TestAccount;

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
        $account = new TestAccount();

        // newly created Account object has balance equals 0
        $this->assertEquals(0, $account->getBalance());
    }

    public function testUser()
    {
        $user = $this->getMock('Hatimeria\BankBundle\Model\User');

        $account = new TestAccount();
        $account->setUser($user);

        $this->assertEquals($user, $account->getUser());
    }

}