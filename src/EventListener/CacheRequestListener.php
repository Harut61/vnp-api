<?php
/**
 * Created by PhpStorm.
 * User: ngpatel
 * Date: 22/5/21
 * Time: 4:30 PM
 */

namespace App\EventListener;

use App\Entity\AdminUser;
use App\Services\CacheService;
use PHPUnit\Util\Json;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CacheRequestListener
{
    protected $redis;
    protected $cacheService;
    protected $tokenStorage;

    public function __construct(\Redis $redis, CacheService $cacheService, TokenStorageInterface $tokenStorage)
    {
        $this->redis = $redis;
        $this->cacheService = $cacheService;
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if(getenv("APP_ENV") != "test") {

            if (!$event->isMasterRequest()) {
                return;
            }
            $request = $event->getRequest();
            if (!in_array($request->getMethod(), ["GET"])) {
                return;
            }

            /** @var AdminUser $user */
            $user = $this->getUser();

            if ($user) {
                $cachePath = $this->cacheService->cachePathGenerator($request->getRequestUri(), $request->get("_route"), $user);

                if ($this->cacheService->checkKeyExist($cachePath)) {
                    $resData = $this->cacheService->getCachedJsonResponse($cachePath);
                    $jsonResponse = new JsonResponse($resData);
                    $jsonResponse->headers->set("content-type", "application/ld+json; charset=utf-8");
                    $event->setResponse($jsonResponse);
                }
            }
        }

    }

    public function getUser(){
        /** @var AdminUser $user */
        $token = $this->tokenStorage->getToken();

        if($token){
            $user = $token->getUser();
            if($user instanceof UserInterface){
                return $token->getUser();
            }
        }

        return false;
    }


}