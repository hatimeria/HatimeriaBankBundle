<?php

namespace Hatimeria\BankBundle\Decimal;

class Decimal
{
    const SCALE = 2;
    
    protected $amount;

    public function __construct($value)
    {
        $this->amount = $value;
    }

    public static function create($value)
    {
        return new self($value);
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function add($decimal)
    {
        if (!$decimal instanceof Decimal) {
            $decimal = new Decimal($decimal);
        }

        return new Decimal(bcadd($this->amount, $decimal->getAmount(), self::SCALE));
    }

    public function mul($decimal)
    {
        if (!$decimal instanceof Decimal) {
            $decimal = new Decimal($decimal);
        }

        return new Decimal(bcmul($this->amount, $decimal->getAmount(), self::SCALE));
    }

    public function div($decimal)
    {
        if (!$decimal instanceof Decimal) {
            $decimal = new Decimal($decimal);
        }

        return new Decimal(bcdiv($this->amount, $decimal->getAmount(), self::SCALE));
    }

    public function sub($decimal)
    {
        if (!$decimal instanceof Decimal) {
            $decimal = new Decimal($decimal);
        }

        return new Decimal(bcsub($this->amount, $decimal->getAmount(), self::SCALE));
    }
    
    public function isEqualTo($decimal)
    {
        if (!$decimal instanceof Decimal) {
            $decimal = new Decimal($decimal);
        }
        
        return bccomp($this->amount, $decimal->getAmount(), self::SCALE) === 0;
    }
    
    public function isGreaterThan($decimal)
    {
        if (!$decimal instanceof Decimal) {
            $decimal = new Decimal($decimal);
        }
        
        return bccomp($this->amount, $decimal->getAmount(), self::SCALE) === 1;
    }
    
    public function isLowerThan($decimal)
    {
        if (!$decimal instanceof Decimal) {
            $decimal = new Decimal($decimal);
        }
        
        return bccomp($this->amount, $decimal->getAmount(), self::SCALE) === -1;
    }

    public function __toString()
    {
        return (string)$this->amount;
    }

}