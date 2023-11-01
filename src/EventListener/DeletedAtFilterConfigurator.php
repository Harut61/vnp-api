<?php


namespace App\EventListener;

use App\Filter\DeletedAtFilter;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Annotations\Reader;

final class DeletedAtFilterConfigurator
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
        /** @var DeletedAtFilter $filter */
        $filter = $this->em->getFilters()->enable('deleted_at_filter');
        $filter->setAnnotationReader($this->reader);
        if ($event->getRequest()->get("trash")) {
            $filter->trash = true;
        }

        if ($event->getRequest()->get("restore")) {
            $filter->trash = true;
        }

        if ($event->getRequest()->get("logs")) {
            $filter->logs = true;
        }

        $filter->isMaster = true;
    }
}
