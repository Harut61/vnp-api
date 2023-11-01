<?php
namespace App\Security\Provider;


use App\Entity\AdminUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AdminUserProvider implements UserProviderInterface
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
        if (!$user instanceof AdminUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        $username = $user->getUsername();

        return $this->fetchUser($username);
    }

    public function fetchUser($email)
    {
        return $this->entityManager->getRepository(AdminUser::class)->findOneBy(["email" => $email]);

    }

    public function supportsClass($class)
    {
        return AdminUser::class === $class;
    }
}