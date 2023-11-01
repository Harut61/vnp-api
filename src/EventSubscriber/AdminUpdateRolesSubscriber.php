<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\AdminUser;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminUpdateRolesSubscriber implements EventSubscriberInterface
{
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [ 'resetAuthToken', EventPriorities::PRE_WRITE ],
            Events::prePersist => 'resetAuthToken'
        ];
    }

    /**
     * @param ViewEvent $event
     *  Update password.
     */
    public function resetAuthToken(ViewEvent $event)
    {
        $object = $event->getControllerResult();
        $postParams = json_decode($event->getRequest()->getContent(), true);

        if(empty($postParams)) return;

        $adminRoles = (array_key_exists("adminRoles", $postParams))? $postParams["adminRoles"] : [] ;
        if ($object instanceof AdminUser && !empty($adminRoles)) {
            $this->updateToken($object);
        }
    }

    /**
     * @param AdminUser $user
     *
     * Updates the user password.
     *
     */
    private function updateToken(AdminUser $user)
    {
        $user->tokens = [];
    }
}
