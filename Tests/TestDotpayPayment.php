<?php

namespace Hatimeria\BankBundle\Tests;

use Hatimeria\BankBundle\Model\DotpayPayment;

class TestDotpayPayment extends DotpayPayment
{
    public function setId($v)
    {
        $this->id = $v;
    }
    
}