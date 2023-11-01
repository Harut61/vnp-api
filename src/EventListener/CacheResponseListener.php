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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CacheResponseListener implements EventSubscriberInterface
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
    public function onKernelResponse(ResponseEvent $event): void
    {
        if(getenv("APP_ENV") != "test") {
            $request = $event->getRequest();
            $routeName  = $request->get('_route');
            $bypassRoute = false;

            if (!in_array($request->getMethod(), ["GET"]) && $event->getResponse()->getStatusCode() == 200) {
                return;
            }
            /** add route in a following list which you want to by pass from redis cache */
            if(in_array($routeName, [
                "profile", "profile_end_user" ,"authentication_checker",
                "bulk_sync","high_level_subject_vne","api_get_index_news_markets_lists",
                "segment_vne", "s3_sub_folder", "segment_vne_update", "source_video_delete",
                "source_videos_pre_signed", "source_videos_cc_upload_presign", "story_cc_upload_presign",
                "story_delete", "stories_pre_signed", "story_type_vne", "story_type_vne_update",
                "vod_upload_presign", "vod_retranscoding", "vod_upload", "vod_initialize", "
                vod_transcoding_queue_update", "vod_pre_signed","end_user_register", ""])){
                return;
            }

            $user = $this->getUser();

            if ($user) {
                $cachePath = $this->cacheService->cachePathGenerator($request->getRequestUri(), $request->get("_route"), $user);

                if (!$this->cacheService->checkKeyExist($cachePath) && !in_array($request->getMethod(), ["POST"])) {

                    $response = $event->getResponse();
                    $this->cacheService->setCachedResponse($cachePath, $response->getContent());
                    $responseContent = $this->cacheService->getCachedResponse($cachePath);
                    $response->setContent($responseContent);
                    $event->setResponse($response);
                }
            }
        }

    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => array( 'onKernelResponse', 0 ),
        );
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