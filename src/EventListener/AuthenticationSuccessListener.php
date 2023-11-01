<?php
namespace App\EventListener;

use ApiPlatform\Core\Bridge\Symfony\Bundle\DependencyInjection\ApiPlatformExtension;
use App\Entity\AdminUser;
use App\Entity\EndUser;
use App\Exception\ApiJsonException;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthenticationSuccessListener
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $response = $event->getResponse();

        $data = $event->getData();
        $user = $event->getUser();
        $validUserClass = false;
        
        if($user instanceof AdminUser || $user instanceof EndUser){
            $validUserClass = true;
        }
        
        if (!$validUserClass) {
            return;
        }

        $data['data'] = array(
            'roles' => $user->getRoles(),
            "email" => $user->email,
            "enabled" => $user->enabled,
            "blocked" => $user->blocked,
        );

        if($user instanceof EndUser){
            $data['data']["signUpStatus"] = $user->signUpStatus;
        }
        
        $event->setData($data);
      
        if(!$user->enabled || $user->blocked) {
            $request = $event->getResponse();
            /* @var $request \Symfony\Component\HttpFoundation\Request */
            
            throw new ApiJsonException("Your access is disabled or blocked", JsonResponse::HTTP_FORBIDDEN);
        }

    }
}