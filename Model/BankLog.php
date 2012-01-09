<?php

namespace Hatimeria\BankBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class BankLog
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var \Hatimeria\BankBundle\Model\Account
     * @ORM\ManyToOne(targetEntity="Hatimeria\BankBundle\Model\Account")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id"))
     */
    protected $account;
    /**
     * @ORM\Column(type="decimal", precision=60, scale=14)
     */
    protected $amount;
    /**
     * @ORM\Column(length="255")
     * @var string
     */
    protected $information;
    /**
     *
     * @ORM\Column(type="datetime", name="created_at", nullable=false)
     * @var \DateTime
     */ 
    protected $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \Hatimeria\BankBundle\Model\Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param \Hatimeria\BankBundle\Model\Account $account
     */
    public function setAccount(Account $account)
    {
        $this->account = $account;
    }

    public function getInformation()
    {
        return $this->information;
    }

    public function setInformation($information)
    {
        $this->information = $information;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function toStoreArray()
    {
        return array(
            'desc'    => $this->information,
            'amount'  => $this->amount,
            'created_at' => $this->createdAt
        );
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

}