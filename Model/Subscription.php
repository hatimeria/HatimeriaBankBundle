<?php

namespace Hatimeria\BankBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * 
 * @author Michal Wujas
 */
abstract class Subscription
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(length="255")
     * @var string
     */
    protected $code;
    /**
     * @ORM\Column(type="date", name="valid_to")
     * @var \DateTime
     */
    protected $validTo;
    /**
     * @ORM\Column(type="date", name="created_at")
     * @var \DateTime
     */
    protected $createdAt;
    
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }
    
    public function getCode()
    {
        return $this->code;
    }
    
    public function setCode($code)
    {
        $this->code = $code;
    }
    
    public function getValidTo()
    {
        return $this->validTo;
    }
    
    public function setValidTo($v)
    {
        $this->validTo = $v;
    }    
    
    public function isValid()
    {
        return $this->id && $this->validTo >= new \DateTime();
    }
}