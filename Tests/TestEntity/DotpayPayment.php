<?php

namespace Hatimeria\BankBundle\Tests\TestEntity;

use Hatimeria\BankBundle\Model\DotpayPayment as BaseDotpayPayment;

class DotpayPayment extends BaseDotpayPayment
{
    public function setId($v)
    {
        $this->id = $v;
    }
    
}