<?php
/**
 * Created by PhpStorm.
 * User: ngpatel
 * Date: 5/4/20
 * Time: 12:36 AM
 */

namespace App\EventListener;

use App\Entity\Users;
use App\Filter\DeletedAtFilter;
use App\Filter\UsersFilter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Annotations\Reader;

final class UsersFilterConfigurator
{
    private $em;
    private $tokenStorage;
    private $reader;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, Reader $reader)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->reader = $reader;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $content = $event->getRequest()->get("content");
        // not allowed all access
        $allowedContent = false;
        $contentType = [
            "watch_histories",
            "wish_lists",
            "likes",
            "follow",
        ];
        foreach ($contentType as $item) {
            if (strpos($event->getRequest()->getRequestUri(), $item)) {
                $allowedContent = true;
            }
        }

        if ($content && !$allowedContent) {
            return ;
        }

        $user = $this->getUser();
         
        if (!$user) {
            return ;
        }


        /** @var UsersFilter $filter */
        $filter = $this->em->getFilters()->enable('users_filter');
           
        /**
         * Check Either user has role_super_admin if yes then
         * check query parameter all=true then bypass this filter.
         */
        $filterAdmin = (in_array("ROLE_SUPER_ADMIN", $user->getRoles())) ? $event->getRequest()->get("all") ? false : true  : true ;

        if ($user && $filterAdmin) {
            $filter->setParameter('id', $user->getId());
        }
        
        $filter->setAnnotationReader($this->reader);
    }

    /**
     * @return null|UserInterface
     */
    private function getUser(): ?UserInterface
    {
        if (!$token = $this->tokenStorage->getToken()) {
            return null;
        }

        $user = $token->getUser();
        return $user instanceof UserInterface ? $user : null;
    }
}
