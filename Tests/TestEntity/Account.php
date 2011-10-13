<?php

namespace Hatimeria\BankBundle\Tests\TestEntity;

use \Hatimeria\BankBundle\Model\Account as BaseAccount;

class Account extends BaseAccount
{
    public function setId($v)
    {
        $this->id = $v;
    }
    
}
