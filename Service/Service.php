<?php

namespace Hatimeria\BankBundle\Service;

/**
 * Service Base Class
 *
 * @author Michal Wujas
 */
abstract class Service
{
    private $code, $costWithoutTax;
    
    public function getCode()
    {
        return $this->code;
    }
    
    public function setCode($code)
    {
        $this->code = $code;
    }
    
    public function getCostWithoutTax()
    {
        return $this->costWithoutTax;
    }
}