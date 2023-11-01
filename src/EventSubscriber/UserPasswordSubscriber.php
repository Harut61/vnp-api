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

class UserPasswordSubscriber implements EventSubscriberInterface
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
            KernelEvents::VIEW => [ 'updatePassword', EventPriorities::PRE_WRITE ],
            Events::prePersist => 'updatePassword'
        ];
    }

    /**
     * @param ViewEvent $event
     *  Update password.
     */
    public function updatePassword(ViewEvent $event)
    {
        $object = $event->getControllerResult();

        if ($object instanceof AdminUser) {
            $this->updateUserPassword($object);
        }
    }

    /**
     * @param AdminUser $user
     *
     * Updates the user password.
     *
     */
    private function updateUserPassword(AdminUser $user)
    {
        if (!empty($user->plainPassword)) {
            /** @var PasswordEncoderInterface $encoder */
            $encoder = $this->encoderFactory->getEncoder($user);

            $user->updatePassword($encoder->encodePassword($user->plainPassword, null));
            $user->plainPassword = null;
        }
    }
}
