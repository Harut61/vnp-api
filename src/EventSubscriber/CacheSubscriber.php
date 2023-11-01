<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\AdminUser;
use App\Entity\Users;
use App\Services\CacheService;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CacheSubscriber implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var CacheService
     */
    private $cacheService;

    public function __construct(ContainerInterface $container, CacheService $cacheService)
    {
        $this->container = $container;
        $this->cacheService = $cacheService;
    }


    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['purgeCache', EventPriorities::POST_WRITE]
        ];
    }



    /**
     * @param ViewEvent $event
     */
    public function purgeCache(ViewEvent $event)
    {

        if(in_array($event->getRequest()->getMethod() , ["POST","PATCH","PUT","DELETE"])){

            $this->purge($event->getRequest()->getRequestUri());
        }
    }

    /**
     * @param $uri
     */
    private function purge($uri)
    {

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $this->cacheService->cachePurgeByUri($user->getId(), $uri);
    }
}
