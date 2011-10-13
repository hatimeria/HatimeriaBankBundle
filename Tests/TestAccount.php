<?php

namespace Hatimeria\BankBundle\Tests;

use \Hatimeria\BankBundle\Model\Account;

class TestAccount extends Account
{
    public function setId($v)
    {
        $this->id = $v;
    }
    
}
