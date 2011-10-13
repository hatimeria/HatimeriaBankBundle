<?php

namespace Hatimeria\BankBundle\Tests\Model;

use Hatimeria\FrameworkBundle\Test\TestCase;

use Hatimeria\BankBundle\Tests\TestEntity\Account;
use Hatimeria\BankBundle\Model\PaymentManager;
use Hatimeria\BankBundle\Tests\TestEntity\DotpayPayment;
use Hatimeria\BankBundle\Bank\Transaction;
use Hatimeria\BankBundle\Bank\CurrencyExchanger;
use Hatimeria\BankBundle\Model\Enum\DotpayPaymentStatus;

class PaymentManagerTest extends TestCase
{
    public function testService()
    {
        $this->getService('payment.manager');
    }
    
    public function getManager($em, $bank)
    {
        return new PaymentManager($em, $bank, 'Hatimeria\BankBundle\Tests\TestEntity');
    }
    
    public function testCreateDotpayPayment()
    {
        $em   = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $bank = $this->getMockBuilder('\Hatimeria\BankBundle\Bank\Bank')->disableOriginalConstructor()->getMock();

        $pm = $this->getManager($em, $bank);

        $account = new Account();
        $account->setId(9);

        $dp = $pm->createDotpayPayment($account);
        $this->assertInstanceOf('Hatimeria\BankBundle\Tests\TestEntity\DotpayPayment', $dp);
        $this->assertNotEmpty($dp->getControl());
        $this->assertEquals($account, $dp->getAccount());
    }

    public function testUpdateDotpayPayment()
    {
        $dp = new DotpayPayment();
        
        $em   = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->once())
            ->method('persist')
            ->with($dp);
        $em->expects($this->once())
            ->method('flush');
        
        $bank = $this->getMockBuilder('\Hatimeria\BankBundle\Bank\Bank')->disableOriginalConstructor()->getMock();

        $dp = new DotpayPayment();

        $pm = $this->getManager($em, $bank);
        $pm->updateDotpayPayment($dp);
    }

    public function testFindDotpayPaymentByControl()
    {
        $repository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $repository->expects($this->once())
            ->method('findOneBy')
            ->with(array('control' => 'test'));
        $em   = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->any())
            ->method('getRepository')
            ->with('Hatimeria\BankBundle\Tests\TestEntity\DotpayPayment')
            ->will($this->returnValue($repository));
        $bank = $this->getMockBuilder('\Hatimeria\BankBundle\Bank\Bank')->disableOriginalConstructor()->getMock();

        $pm = $this->getManager($em, $bank);
        $pm->findDotpayPaymentByControl('test');
    }

    public function testValidateDotpayPaymentPositive()
    {
        $repository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $repository->expects($this->once())
            ->method('findOneBy')
            ->with(array('control' => 'test1234'))
            ->will($this->returnValue($this->getMock('Hatimeria\BankBundle\Tests\TestEntity\DotpayPayment')));

        $em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->once())
            ->method('getRepository')
            ->with('Hatimeria\BankBundle\Tests\TestEntity\DotpayPayment')
            ->will($this->returnValue($repository));
        $bank = $this->getMockBuilder('\Hatimeria\BankBundle\Bank\Bank')->disableOriginalConstructor()->getMock();

        $response = $this->getMock('\Hatimeria\DotpayBundle\Response\Response');
        $response->expects($this->atLeastOnce())
            ->method('getControl')
            ->will($this->returnValue('test1234'));

        $event = $this->getMockBuilder('\Hatimeria\DotpayBundle\Event\ValidationEvent')->disableOriginalConstructor()->getMock();
        $event->expects($this->atLeastOnce())
            ->method('getSubject')
            ->will($this->returnValue($response));
        $event->expects($this->atLeastOnce())
            ->method('markAsValid');

        $pm = $this->getManager($em, $bank);
        $pm->validateDotpayPayment($event);
    }

    public function testValidateDotpayPaymentNegative()
    {
        $repository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $repository->expects($this->once())
            ->method('findOneBy')
            ->with(array('control' => 'test1234'));

        $em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->once())
            ->method('getRepository')
            ->with('Hatimeria\BankBundle\Tests\TestEntity\DotpayPayment')
            ->will($this->returnValue($repository));
        $bank = $this->getMockBuilder('\Hatimeria\BankBundle\Bank\Bank')->disableOriginalConstructor()->getMock();

        $response = $this->getMock('\Hatimeria\DotpayBundle\Response\Response');
        $response->expects($this->atLeastOnce())
            ->method('getControl')
            ->will($this->returnValue('test1234'));

        $event = $this->getMockBuilder('\Hatimeria\DotpayBundle\Event\ValidationEvent')->disableOriginalConstructor()->getMock();
        $event->expects($this->atLeastOnce())
            ->method('getSubject')
            ->will($this->returnValue($response));
        $event->expects($this->atLeastOnce())
            ->method('markAsInvalid');

        $pm = $this->getManager($em, $bank);
        $pm->validateDotpayPayment($event);
    }

    public function testExecuteDotpayPaymentFinished()
    {
        $dp = $this->getMock('Hatimeria\BankBundle\Tests\TestEntity\DotpayPayment');
        $dp->expects($this->atLeastOnce())
            ->method('isFinished')
            ->will($this->returnValue(true));

        $repository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $repository->expects($this->once())
            ->method('findOneBy')
            ->with(array('control' => 'test1234'))
            ->will($this->returnValue($dp));

        $em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->once())
            ->method('getRepository')
            ->with('Hatimeria\BankBundle\Tests\TestEntity\DotpayPayment')
            ->will($this->returnValue($repository));
        $bank = $this->getMockBuilder('\Hatimeria\BankBundle\Bank\Bank')->disableOriginalConstructor()->getMock();

        $response = $this->getMock('\Hatimeria\DotpayBundle\Response\Response');
        $response->expects($this->atLeastOnce())
            ->method('getControl')
            ->will($this->returnValue('test1234'));

        $event = $this->getMockBuilder('\Hatimeria\DotpayBundle\Event\Event')->disableOriginalConstructor()->getMock();
        $event->expects($this->atLeastOnce())
            ->method('getSubject')
            ->will($this->returnValue($response));
        $event->expects($this->atLeastOnce())
            ->method('setResult')
            ->with(false);

        $pm = $this->getManager($em, $bank);
        $pm->executeDotpayPayment($event);
    }

    /**
     * Test case whtne status is other then MADE
     */
    public function testExecuteDotpayPaymentOtherStatus()
    {
        try {
            $dp = $this->getMock('Hatimeria\BankBundle\Tests\TestEntity\DotpayPayment');
            $dp->expects($this->atLeastOnce())
                ->method('isFinished')
                ->will($this->returnValue(false));

            $repository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
            $repository->expects($this->once())
                ->method('findOneBy')
                ->with(array('control' => 'test1234'))
                ->will($this->returnValue($dp));

            $em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
            $em->expects($this->once())
                ->method('getRepository')
                ->with('Hatimeria\BankBundle\Tests\TestEntity\DotpayPayment')
                ->will($this->returnValue($repository));
            $bank = $this->getMockBuilder('\Hatimeria\BankBundle\Bank\Bank')->disableOriginalConstructor()->getMock();

            $response = $this->getMock('\Hatimeria\DotpayBundle\Response\Response');
            $response->expects($this->atLeastOnce())
                ->method('getControl')
                ->will($this->returnValue('test1234'));
            $response->expects($this->atLeastOnce())
                ->method('isStatusMade')
                ->will($this->returnValue(false));

            $event = $this->getMockBuilder('\Hatimeria\DotpayBundle\Event\Event')->disableOriginalConstructor()->getMock();
            $event->expects($this->atLeastOnce())
                ->method('getSubject')
                ->will($this->returnValue($response));

            $pm = $this->getManager($em, $bank);
            $pm->executeDotpayPayment($event);
        }
        catch (\Exception $e) {
            return $this->assertEquals('Only status MADE is implemented :/', $e->getMessage());
        }

        $this->fail();
    }

    public function testExecuteDotpayPayment()
    {
        $account = new Account();

        $t = new Transaction($account);
        $t->setAmount(431);
        $t->setCurrency(CurrencyExchanger::PLN);
        $t->setInformation('Doładowanie poprzez dotpay 3');

        $dp = $this->getMock('Hatimeria\BankBundle\Tests\TestEntity\DotpayPayment');
        $dp->expects($this->atLeastOnce())
            ->method('isFinished')
            ->will($this->returnValue(false));
        $dp->expects($this->once())
            ->method('getAccount')
            ->will($this->returnValue($account));
        $dp->expects($this->once())
            ->method('setStatus')
            ->with(DotpayPaymentStatus::FINISHED);

        $repository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $repository->expects($this->once())
            ->method('findOneBy')
            ->with(array('control' => 'test1234'))
            ->will($this->returnValue($dp));

        $em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->once())
            ->method('getRepository')
            ->with('Hatimeria\BankBundle\Tests\TestEntity\DotpayPayment')
            ->will($this->returnValue($repository));
        $bank = $this->getMockBuilder('\Hatimeria\BankBundle\Bank\Bank')->disableOriginalConstructor()->getMock();
        $bank->expects($this->atLeastOnce())
            ->method('deposit')
            ->with($t);

        $response = $this->getMock('\Hatimeria\DotpayBundle\Response\Response');
        $response->expects($this->atLeastOnce())
            ->method('getControl')
            ->will($this->returnValue('test1234'));
        $response->expects($this->atLeastOnce())
                ->method('isStatusMade')
                ->will($this->returnValue(true));
        $response->expects($this->once())
            ->method('getTransactionId')
            ->will($this->returnValue(3));
        $response->expects($this->atLeastOnce())
            ->method('getAmount')
            ->will($this->returnValue(431));

        $event = $this->getMockBuilder('\Hatimeria\DotpayBundle\Event\Event')->disableOriginalConstructor()->getMock();
        $event->expects($this->atLeastOnce())
            ->method('getSubject')
            ->will($this->returnValue($response));
        $event->expects($this->atLeastOnce())
            ->method('setResult')
            ->with(true);

        $pm = $this->getManager($em, $bank);
        $pm->executeDotpayPayment($event);
    }

}