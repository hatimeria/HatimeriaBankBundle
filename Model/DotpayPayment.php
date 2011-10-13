<?php

namespace Hatimeria\BankBundle\Model;
 
use Doctrine\ORM\Mapping as ORM;

use Hatimeria\BankBundle\Model\Enum\DotpayPaymentStatus;

/**
 * @ORM\MappedSuperclass
 * @ORM\Table(name="dotpay_payment")
 */
class DotpayPayment
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     *
     * @var int
     */
    protected $id;
    /**
     * @ORM\Column(type="string", unique="true")
     *
     * @var string
     */
    protected $control;
    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     *
     * @var float
     */
    protected $amount;
    /**
     * @ORM\Column(type="boolean", name="is_charge")
     *
     * @var bool
     */
    protected $isCharge;
    /**
     * @ORM\ManyToOne(targetEntity="Hatimeria\BankBundle\Model\Account", cascade={"merge"})
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     */
    protected $account;
    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $status;
    
    public function __construct()
    {
        $this->status   = DotpayPaymentStatus::NONE;
        $this->isCharge = true;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getControl()
    {
        return $this->control;
    }

    /**
     * @param string $control
     */
    public function setControl($control)
    {
        $this->control = $control;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return boolean
     */
    public function getIsCharge()
    {
        return $this->isCharge;
    }

    /**
     * @param boolean $isCharge
     */
    public function setIsCharge($isCharge)
    {
        $this->isCharge = $isCharge;
    }

    /**
     * @return \Hatimeria\BankBundle\Model\Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    public function setAccount($account)
    {
        $this->account = $account;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function isFinished()
    {
        return DotpayPaymentStatus::FINISHED === $this->status;
    }

}