<?php

namespace Hatimeria\BankBundle\Tests;

use \Hatimeria\BankBundle\Model\BankLog;

class TestBankLog extends BankLog
{
    public function setId($v)
    {
        $this->id = $v;
    }
    
}