<?php

namespace Hatimeria\BankBundle\Tests\Entity;

use Hatimeria\FrameworkBundle\Test\TestCase;

use Hatimeria\BankBundle\Model\BankLogManager;
use Hatimeria\BankBundle\Bank\Transaction;
use Hatimeria\BankBundle\Tests\TestAccount;
use Hatimeria\BankBundle\Model\BankLog;

class BankLogManagerTest extends TestCase
{
    public function testService()
    {
        $this->getService('bank_log.manager');
    }

    public function testCreateLog()
    {
        $em  = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $blm = new BankLogManager($em);

        $log = $blm->createLog();
        $this->assertInstanceOf('Hatimeria\BankBundle\Model\BankLog', $log);
        $this->assertEquals(new BankLog(), $log);
    }

    public function testCreateLogFromTransaction()
    {
        $em  = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $blm = new BankLogManager($em);

        $account = new TestAccount();

        $t = new Transaction($account);
        $t->setAmount(1000);
        $t->setInformation('Test information');

        $log = $blm->createLog($t);

        $this->assertInstanceOf('Hatimeria\BankBundle\Model\BankLog', $log);
        $this->assertEquals('Test information', $log->getInformation());
        $this->assertEquals($account, $log->getAccount());
        $this->assertEquals(1000, $log->getAmount());
    }

    public function testGetRepository()
    {
        $repository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $em  = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->once())
            ->method('getRepository')
            ->with('Hatimeria\BankBundle\Model\BankLog')
            ->will($this->returnValue($repository));
        
        $blm  = new BankLogManager($em);
        $repo = $blm->getRepository();

        $this->assertEquals($repository, $repo);
    }

    public function testUpdateLog()
    {
        $log = new BankLog();

        $em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->once())
            ->method('persist')
            ->with($log);

        $blm = new BankLogManager($em);
        $blm->updateLog($log);
    }

    public function testFindByAccount()
    {
        $repository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $repository->expects($this->once())
            ->method('findBy')
            ->with(array('account' => 9));

        $em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repository));

        $account = new TestAccount();
        $account->setId(9);

        $blm = new BankLogManager($em);
        $blm->findByAccount($account);
    }

}
