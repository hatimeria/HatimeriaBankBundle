<?php

namespace Hatimeria\BankBundle\Tests\Bank;

use Hatimeria\FrameworkBundle\Test\TestCase;

use Hatimeria\BankBundle\Currency\Exchanger;
use Hatimeria\BankBundle\Currency\CurrencyCode;

class ExchangerTest extends TestCase
{
    /**
     * @return \Hatimeria\BankBundle\Bank\CurrencyExchanger
     */
    private function getExchanger()
    {
        return new Exchanger();
    }

    public function testService()
    {
        $this->getService('bank.exchanger');
    }

    public function testExchange()
    {
        $exchanger = $this->getExchanger();

        // calculate currency to mega cents
        $cents = $exchanger->exchange(123, CurrencyCode::PLN, CurrencyCode::VIRTUAL);
        $this->assertEquals(1230, $cents);

        // calculate currency from mega cents
        $plns = $exchanger->exchange(100, CurrencyCode::VIRTUAL, CurrencyCode::PLN);
        $this->assertEquals(10, $plns);
    }

    /**
     * Exchanger should change to opposite currency without selected destination currency
     */
    public function testDefaultBehaviour()
    {
        $exchanger = $this->getExchanger();

        $result = $exchanger->exchange(1230, CurrencyCode::VIRTUAL);
        $this->assertEquals(123, $result);
    }

    public function testSameCurrencyType()
    {
        $exchanger = $this->getExchanger();

        $result = $exchanger->exchange(132, CurrencyCode::VIRTUAL, CurrencyCode::VIRTUAL);
        $this->assertEquals(132, $result);

        $result = $exchanger->exchange(213, CurrencyCode::PLN, CurrencyCode::PLN);
        $this->assertEquals(213, $result);
    }

}