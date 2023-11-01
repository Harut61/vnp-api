<?php
namespace App\EventListener;

use App\Entity\AdminUser;
use App\Entity\EndUser;
use App\Enums\UserRoleEnum;
use App\Services\Handlers\AdminUserHandlers;
use App\Services\Handlers\EndUserHandlers;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTEncodedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Namshi\JOSE\JWT;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTLexikListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var JWTTokenManagerInterface
     */
    private $jwtManager;

    /**
     * @var AdminUserHandlers
     */
    private $adminUserHandlers;

    /**
     * @var EndUserHandlers
     */
    private $endUserHandlers;

    /**
     * JWTLexikListener constructor.
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $entityManager
     * @param JWTTokenManagerInterface $jwtManager
     * @param AdminUserHandlers $adminUserHandlers
     * @param EndUserHandlers $endUserHandlers
     */
    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager, JWTTokenManagerInterface $jwtManager,
                                AdminUserHandlers $adminUserHandlers,
                                EndUserHandlers $endUserHandlers
    )
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->jwtManager = $jwtManager;
        $this->adminUserHandlers = $adminUserHandlers;
        $this->endUserHandlers = $endUserHandlers;
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {

        $request = $this->requestStack->getCurrentRequest();
        $user = $event->getUser();
        if (!$user instanceof UserInterface) {
            return;
        }

        if($user instanceof AdminUser) {
            /** @var AdminUser $user */
            $user = $this->adminUserHandlers->repo->find($user->getId());
        } else if($user instanceof EndUser)
        {
            /** @var EndUser $user */
            $user = $this->endUserHandlers->repo->find($user->getId());
        }


        $payload = $event->getData();
        $ip = (getenv("APP_ENV") === "test") ? "1.2.3.4" : $request->getClientIp();

        $payload['ip'] = $ip;
        $token = md5(time());
        if ($user instanceof AdminUser) {
            $payload['admin_id'] = $user->getId();
        } else if($user instanceof EndUser)
        {
            $payload['end_user_id'] = $user->getId();
        }



        $header = $event->getHeader();
        $header['cty'] = 'JWT';

        $count = (is_array($user->tokens)) ? count($user->tokens) : 0;
        $payload['token'] = $token =  $count."_".$token;
        $user->tokens[$token] = $payload;
        $user->lastLogin = new \DateTime();
        if($user instanceof AdminUser) {
            $this->adminUserHandlers->save($user);
            $this->adminUserHandlers->insertAudit("auth", "Login", $ip, $user);
        }else if($user instanceof EndUser){
            $this->endUserHandlers->save($user);
            $this->endUserHandlers->insertAudit("auth", "Login", $ip, $user);
        }



        $event->setData($payload);
        $event->setHeader($header);
    }

    public function onJWTDecoded(JWTDecodedEvent $event)
    {

        $request = $this->requestStack->getCurrentRequest();

        $payload = $event->getPayload();
        if(array_key_exists("end_user_id" , $payload)){
            /** @var EndUser $user */
            $user = $this->endUserHandlers->repo->find($payload["end_user_id"]);
        } else {
            /** @var AdminUser $user */
            $user = $this->adminUserHandlers->repo->find($payload["admin_id"]);
        }

        $tokens = ($user->tokens)? $user->tokens : [] ;

        $numberOfDevices = $user->numberOfDevices;
        $tokenValid = false;
        $usersToken = [];
        $roles = $user->getRoles();

        if(!$user->enabled){
            $event->markAsInvalid();
            return;
        }

        // disable device limit for super admin
        if(in_array(UserRoleEnum::ROLE_SUPER_ADMIN, $roles)) return;

        if($numberOfDevices > 0) {
            while($numberOfDevices > 0)
            {
                $usersToken[] = array_key_last($tokens);
                $numberOfDevices--;
            }
        }

        if(in_array($payload["token"] , $usersToken))
        {
            $tokenValid = true;
        }

/*        $ip = (getenv("APP_ENV") === "test") ? "1.2.3.4" : $request->getClientIp();
        if (!isset($payload['ip']) || $payload['ip'] !== $ip || (!$tokenValid)) {*/

        if ((!$tokenValid)) {

            unset($tokens[$payload["token"]]);
            $user->tokens = $tokens;
            if(array_key_exists("end_user_id" , $payload)){
                $this->endUserHandlers->save($user);
            } else {
                /** @var AdminUser $user */
                $this->adminUserHandlers->save($user);
            }

            $event->markAsInvalid();
        }
    }

}