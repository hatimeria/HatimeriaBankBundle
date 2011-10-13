<?php

namespace Hatimeria\BankBundle\Tests\Model;

use Hatimeria\FrameworkBundle\Test\TestCase;

use Hatimeria\BankBundle\Model\BankLogManager;
use Hatimeria\BankBundle\Bank\Transaction;
use Hatimeria\BankBundle\Tests\TestEntity\Account;
use Hatimeria\BankBundle\Tests\TestEntity\BankLog;

class BankLogManagerTest extends TestCase
{
    public function testService()
    {
        $this->getService('bank_log.manager');
    }

    public function testCreateLog()
    {
        $em  = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $blm = $this->getManager($em);

        $log = $blm->createLog();
        $this->assertInstanceOf('Hatimeria\BankBundle\Model\BankLog', $log);
        $this->assertEquals(new BankLog(), $log);
    }

    public function testCreateLogFromTransaction()
    {
        $em  = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $blm = $this->getManager($em);

        $account = new Account();

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
            ->with('Hatimeria\BankBundle\Tests\TestEntity\BankLog')
            ->will($this->returnValue($repository));
        
        $blm = $this->getManager($em);
        $repo = $blm->getRepository();

        $this->assertEquals($repository, $repo);
    }
    
    public function getManager($em)
    {
        return new BankLogManager($em, 'Hatimeria\BankBundle\Tests\TestEntity');
    }

    public function testUpdateLog()
    {
        $log = new BankLog();

        $em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->once())
            ->method('persist')
            ->with($log);

        $blm = $this->getManager($em);
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

        $account = new Account();
        $account->setId(9);

        $blm = $this->getManager($em);
        $blm->findByAccount($account);
    }

}
