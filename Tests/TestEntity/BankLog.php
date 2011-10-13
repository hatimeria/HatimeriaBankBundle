<?php

namespace Hatimeria\BankBundle\Tests\TestEntity;

use \Hatimeria\BankBundle\Model\BankLog as BaseBankLog;

class BankLog extends BaseBankLog
{
    public function setId($v)
    {
        $this->id = $v;
    }
    
}