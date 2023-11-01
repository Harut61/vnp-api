<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\AdminUser;
use App\Entity\SourceVideo;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CreatedBySubscriber implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [ 'createdBy', EventPriorities::PRE_WRITE ],
            Events::prePersist => [ 'createdBy', Events::prePersist ],
        ];
    }

    /**
     * @param ViewEvent $event
     *  Update password.
     */
    public function createdBy(ViewEvent $event)
    {
        $object = $event->getControllerResult();
        if($event->getRequest()->getMethod() !== "POST"){
            return;
        }
        if($object instanceof SourceVideo ) {
            return;
        }

        if (is_object($object) && property_exists($object,"createdBy")) {
            $this->setCreatedBy($object);
        }
    }

    /**
     * @param $objectClass
     */
    private function setCreatedBy($objectClass)
    {
        /** @var AdminUser $user */
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $objectClass->createdBy = $user;
    }
}
