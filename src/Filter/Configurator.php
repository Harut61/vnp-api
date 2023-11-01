<?php
/**
 * Created by PhpStorm.
 * User: ngpatel
 * Date: 10/9/19
 * Time: 7:32 PM
 */

namespace App\Filter;

use App\Entity\Users;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Annotations\Reader;

class Configurator
{
    protected $em;
    protected $tokenStorage;
    protected $reader;

    public function __construct(EntityManager $em, TokenStorageInterface $tokenStorage, Reader $reader)
    {
        $this->em              = $em;
        $this->tokenStorage    = $tokenStorage;
        $this->reader          = $reader;
    }

    public function onKernelRequest()
    {
        $user = $this->getUser();

        /** @var $user Users */
        /** TODO check if query param deletedat exist then bypass this filter */
//        if (!empty($user) && !in_array("ROLE_SUPER_ADMIN", $user->getRoles())) {
            $filter = $this->em->getFilters()->enable('deleted_at_filter');
            $filter->setAnnotationReader($this->reader);
//        }
    }

    private function getUser()
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            return null;
        }

        $user = $token->getUser();

        if (!($user instanceof UserInterface)) {
            return null;
        }

        return $user;
    }
}
