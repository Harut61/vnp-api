<?php
namespace App\Security\Guard;

use App\Entity\AdminUser;
use App\Entity\EndUser;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator as BaseAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\PayloadAwareUserProviderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\ChainUserProvider;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class JWTTokenAuthenticator extends BaseAuthenticator
{
    /** @var EntityManagerInterface  $entityManager */
    protected $entityManager;

    public function __construct(JWTTokenManagerInterface $jwtManager, EventDispatcherInterface $dispatcher, TokenExtractorInterface $tokenExtractor, TokenStorageInterface $tokenStorage,  EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($jwtManager, $dispatcher, $tokenExtractor, $tokenStorage);
    }


    /**
     * Loads the user to authenticate.
     *
     * @param UserProviderInterface $userProvider An user provider
     * @param array                 $payload      The token payload
     * @param string                $identity     The key from which to retrieve the user "username"
     *
     * @return UserInterface
     */
    protected function loadUser(UserProviderInterface $userProvider, array $payload, $identity)
    {
        if ($userProvider instanceof PayloadAwareUserProviderInterface) {
            return $userProvider->loadUserByUsernameAndPayload($identity, $payload);
        }

        if ($userProvider instanceof ChainUserProvider) {
            foreach ($userProvider->getProviders() as $provider) {
                try {
                    if ($provider instanceof PayloadAwareUserProviderInterface) {

                        return $provider->loadUserByUsernameAndPayload($identity, $payload);
                    }

                    return $provider->loadUserByUsername($identity);
                } catch (UsernameNotFoundException $e) {
                    // try next one
                }
            }

            $ex = new UsernameNotFoundException(sprintf('There is no user with name "%s".', $identity));
            $ex->setUsername($identity);
            throw $ex;
        }
            return $userProvider->loadUserByUsername($identity);
    }
}