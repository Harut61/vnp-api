<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\AdminUser;
use App\Enums\UserStatusEnum;
use App\Services\Handlers\AdminUserHandlers;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminUserUpdateSubscriber implements EventSubscriberInterface
{
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @var AdminUserHandlers
     */
    private $adminUserHandlers;

    public function __construct(EncoderFactoryInterface $encoderFactory, AdminUserHandlers $adminUserHandlers)
    {
        $this->encoderFactory = $encoderFactory;
        $this->adminUserHandlers = $adminUserHandlers;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [ 'updateUser', EventPriorities::PRE_WRITE ],
            Events::prePersist => 'updateUser'
        ];
    }

    /**
     * @param ViewEvent $event
     *  Update password.
     */
    public function updateUser(ViewEvent $event)
    {
        if(getenv("APP_ENV") == "test"){
            return;
        }
        $object = $event->getControllerResult();
        $postParams = json_decode($event->getRequest()->getContent(), true);
        if ($object instanceof AdminUser && $postParams) {

            if((array_key_exists("mobileNumber",$postParams) || array_key_exists("email",$postParams)) && (!empty($postParams["mobileNumber"])|| !empty($postParams["email"]))){
                $this->resetEmail($object);
            }
        }
    }

    /**
     * @param AdminUser $user
     *
     * Updates the user password.
     *
     */
    private function resetEmail(AdminUser $user)
    {
        $user = $this->adminUserHandlers->setEmailVerificationToken($user);
        $user->userStatus = UserStatusEnum::PENDING;
        $this->adminUserHandlers->sendEmailVerification($user);
    }
}
