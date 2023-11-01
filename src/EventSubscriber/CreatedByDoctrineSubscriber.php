<?php

namespace App\EventSubscriber;

use App\Entity\AdminUser;
use App\Entity\SourceVideo;
use App\Entity\Story;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class CreatedByDoctrineSubscriber implements EventSubscriber
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public  function getSubscribedEvents() : array
    {
        return [
            Events::prePersist => "prePersist",
            Events::preUpdate => "preUpdate"
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {

        $this->createdBy('persist', $args);
    }


    public function preUpdate(LifecycleEventArgs $args): void
    {
         $this->createdBy('preUpdate', $args );
    }


    /**
     * @param string $action
     * @param LifecycleEventArgs $args
     */
    public function createdBy(string $action, LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if(($entity instanceof SourceVideo || $entity instanceof Story) && $action == "preUpdate") {
            return;
        }

        if (property_exists($entity,"createdBy")) {
            if($this->container->get('security.token_storage')->getToken()){
                /** @var AdminUser $user */
                $user = $this->container->get('security.token_storage')->getToken()->getUser();
                $entity->createdBy = $user;
            }
        }
    }

}
