<?php
namespace Hatimeria\BankBundle\Tests\Bank;

use Hatimeria\FrameworkBundle\Test\TestCase;

use Hatimeria\BankBundle\Tests\TestEntity\Account;
use Hatimeria\BankBundle\Bank\CurrencyExchanger;
use Hatimeria\BankBundle\Bank\Bank;
use Hatimeria\BankBundle\Bank\Transaction;
use Hatimeria\BankBundle\Bank\BankException;

/**
 * Investments test
 *
 * @author michal
 */
class BankTest extends TestCase
{
    private function getAccount($balance)
    {
        $account = new Account();
        $account->setBalance($balance);
        
        return $account;
    }

    /**
     * @param int $balance
     * @param \Hatimeria\BankBundle\Tests\TestAccount $account
     */
    private function assertBalance($balance, Account $account)
    {
        $this->assertEquals($balance, $account->getBalance());
    }

    /**
     * Test if service is available
     */
    public function testService()
    {
        $this->getService("bank");
    }

    public function testAddAmountToAccount()
    {
        $account = $this->getAccount(100);

        $exchanger = $this->getMock('\Hatimeria\BankBundle\Bank\CurrencyExchanger');
        $exchanger->expects($this->atLeastOnce())
            ->method('exchange')
            ->with(25, CurrencyExchanger::PLN)
            ->will($this->returnValue(250));

        $em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->atLeastOnce())
                ->method('persist');

        $blm = $this->getMockBuilder('\Hatimeria\BankBundle\Model\BankLogManager')->disableOriginalConstructor()->getMock();

        $bank = new Bank($exchanger, $em, $blm);
        
        // add amount in currency to account ballance
        $bank->addAmountToAccount(25, CurrencyExchanger::PLN, $account);
        
        $this->assertBalance(350, $account);
    }

    public function testDeposit()
    {
        $account = $this->getAccount(135);

        $exchanger = $this->getMock('\Hatimeria\BankBundle\Bank\CurrencyExchanger');
        $exchanger->expects($this->atLeastOnce())
            ->method('exchange')
            ->with(45, CurrencyExchanger::PLN)
            ->will($this->returnValue(450));

        $em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->atLeastOnce())->method('persist');

        $bl = $this->getMock('\Hatimeria\BankBundle\Model\BankLog');

        $blm = $this->getMockBuilder('\Hatimeria\BankBundle\Model\BankLogManager', array('createLog'))->disableOriginalConstructor()->getMock();
        $blm->expects($this->atLeastOnce())
            ->method('createLog')
            ->will($this->returnValue($bl));
        $blm->expects($this->atLeastOnce())
            ->method('updateLog');

        $t = new Transaction($account);
        $t->setAmount(45);
        $t->setCurrency(CurrencyExchanger::PLN);
        $t->setInformation('Test deposit');

        $bank = new Bank($exchanger, $em, $blm);
        $bank->deposit($t);

        $this->assertBalance(585, $account);
    }

    public function testTransferFunds()
    {
        /* Orginal account */
        $oa = $this->getAccount(295);
        /* Destination account */
        $da = $this->getAccount(75);

        $exchanger = $this->getMock('\Hatimeria\BankBundle\Bank\CurrencyExchanger');

        $em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->atLeastOnce())->method('persist');

        $blm = $this->getMockBuilder('\Hatimeria\BankBundle\Model\BankLogManager')->disableOriginalConstructor()->getMock();

        $bank = new Bank($exchanger, $em, $blm);

        $bank->transferFunds($oa, $da, 45);

        $this->assertBalance(250, $oa);
        $this->assertBalance(120, $da);
    }

    public function testInsufficientFunds()
    {
        try {
            /* Orginal account */
            $oa = $this->getAccount(10);
            /* Destination account */
            $da = $this->getAccount(75);

            $exchanger = $this->getMock('\Hatimeria\BankBundle\Bank\CurrencyExchanger');
            $em        = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
            $blm       = $this->getMockBuilder('\Hatimeria\BankBundle\Model\BankLogManager')->disableOriginalConstructor()->getMock();
            $bank      = new Bank($exchanger, $em, $blm);

            $bank->transferFunds($oa, $da, 45);
        }
        catch (BankException $e) {
            $this->assertEquals('Not enough founds', $e->getMessage());
            return;
        }
        
        $this->fail();
    }

    public function testGetExchanger()
    {
        $exchanger = $this->getMock('\Hatimeria\BankBundle\Bank\CurrencyExchanger');
        $em        = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $blm       = $this->getMockBuilder('\Hatimeria\BankBundle\Model\BankLogManager')->disableOriginalConstructor()->getMock();

        $bank = new Bank($exchanger, $em, $blm);

        $result = $bank->getExchanger();
        $this->assertEquals($exchanger, $result);
    }

    public function testGetDepositHistoryQuery()
    {
        $qb = $this->getMockBuilder('\Doctrine\ORM\QueryBuilder')->disableOriginalConstructor()->getMock();
        $qb->expects($this->once())
            ->method('andWhere')
            ->with('e.account = 99');
        
        $repository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $repository->expects($this->once())
            ->method('createQueryBuilder')
            ->with('e')
            ->will($this->returnValue($qb));

        $exchanger = $this->getMock('\Hatimeria\BankBundle\Bank\CurrencyExchanger');
        $em        = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $blm       = $this->getMockBuilder('\Hatimeria\BankBundle\Model\BankLogManager')->disableOriginalConstructor()->getMock();
        $blm->expects($this->atLeastOnce())
            ->method('getRepository')
            ->will($this->returnValue($repository));

        $bank = new Bank($exchanger, $em, $blm);

        $account = $this->getAccount(1000);
        $account->setId(99);

        $result = $bank->getDepositHistoryQuery($account);
        $result->andHaving('test');
    }

}