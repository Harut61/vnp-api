<?php
namespace App\Security\Provider;
use App\Entity\AdminUser;
use App\Entity\EndUser;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
class EndUserProvider implements UserProviderInterface
{
    /** @var  EntityManagerInterface $entityManager */
    protected $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function loadUserByUsername($username)
    {
        return $this->fetchUser($username);
    }
    public function loadUserByEmail($email)
    {
        return $this->fetchUser($email);
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof EndUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        $email = $user->getEmail();

        return $this->fetchUser($email);
    }
    public function fetchUser($email)
    {
        $user =  $this->entityManager->getRepository(EndUser::class)->findOneBy(["email" => $email]);

        if(empty($user)) {
            $user =  $this->entityManager->getRepository(AdminUser::class)->findOneBy(["email" => $email]);
        }
        return $user;
    }

    /**
     * @param $googleId
     * @param $email
     * @return null|object
     */
    public function fetchUserByGoogleToken($googleId, $email)
    {
        return $this->entityManager->getRepository(EndUser::class)->findOneBy(["googleId" => $googleId, "email" => $email]);
    }


    /**
     * @param $appleId
     * @return null|object
     */
    public function fetchUserByAppleToken($appleId)
    {
        return $this->entityManager->getRepository(EndUser::class)->findOneBy(["appleId" => $appleId]);
    }

    public function supportsClass($class)
    {
        return EndUser::class === $class;
    }
}