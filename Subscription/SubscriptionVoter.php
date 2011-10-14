<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hatimeria\BankBundle\Subscription;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use \DateTime;

/**
 * If user has subscription
 */
class SubscriptionVoter implements VoterInterface
{
    private $freeValidTo, $prefix;

    /**
     * Constructor.
     *
     * @param string $prefix The role prefix
     */
    public function __construct($freeValidTo)
    {
        $this->freeValidTo = new DateTime($freeValidTo);
        $this->prefix = "SUBSCRIPTION_";
    }

    /**
     * {@inheritdoc}
     */
    public function supportsAttribute($attribute)
    {
        return 0 === strpos($attribute, $this->prefix);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $result = VoterInterface::ACCESS_ABSTAIN;
        $roles = $this->extractRoles($token);
        
        foreach ($attributes as $attribute) {
            if (!$this->supportsAttribute($attribute)) {
                continue;
            }
            
            $user  = $token->getUser();
            
            if(!is_object($user)) {
                return VoterInterface::ACCESS_DENIED;
            }
            
            if($this->freeValidTo > new DateTime()) {
                return VoterInterface::ACCESS_GRANTED;
            } else {
                $subscription = $user->getSubscription();
                $validSubscription = is_object($subscription) && $subscription->isValid();
                if($validSubscription && $attribute == 'SUBSCRIPTION_ENABLED') {
                    return VoterInterface::ACCESS_GRANTED;
                }
                
                $code = $subscription->getCode();
                
                if(strpos($code, strtolower($attribute)) === 0) {
                    return VoterInterface::ACCESS_GRANTED;
                }
            } 
            
            return VoterInterface::ACCESS_DENIED;
        }

        return $result;
    }

    protected function extractRoles(TokenInterface $token)
    {
        return $token->getRoles();
    }
}
