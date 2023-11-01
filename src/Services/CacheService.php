<?php

namespace App\Services;

use App\Entity\AdminUser;
use App\Enums\UserRoleEnum;
use ContainerAGWxnmg\get_Container_Private_CacheClearerService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class CacheService
{

    /**
     *
     * @var \Predis\Client
     */
    private $redis;
    protected $container;

    public function __construct(ContainerInterface $container, \Redis $client)
    {
        $this->container = $container;
        $this->redis = $client;
    }

    public function ttl($path)
    {
        return $this->redis->ttl($path);
    }

    public function retrieve($path)
    {
        $content = $this->redis->get($path);
        if (!$content) {
            return null;
        }
        return $content;
    }

    public function store($path, $content)
    {
        $this->redis->set($path, $content);
        $this->redis->expire($path, 600);
    }

    public function purge($path)
    {
        $this->redis->del($path);
    }

    /**
     * @param $path
     */
    public function purgeAll($path)
    {

        $keys = $this->redis->keys($path);

        if ($keys) {
            foreach ($keys as $key) {
                $this->redis->del($key);
            }
        }
    }

    /**
     * @param $cachePath
     * @return bool|mixed
     */
    public function getCachedJsonResponse($cachePath)
    {
        $cache = $this->retrieve("$cachePath:json");
        if ($cache) {
            return json_decode($cache, true);
        }
        return FALSE;
    }

    /**
     * @param $cachePath
     * @param $result
     */
    public function setCachedJsonResponse($cachePath, $result)
    {
        $this->store("$cachePath:json", json_encode($result));
    }

    /**
     * @param $cachePath
     * @return bool|mixed
     */
    public function getCachedResponse($cachePath)
    {
        $cache = $this->retrieve("$cachePath:json");
        if ($cache) {
            return $cache;
        }
        return FALSE;
    }

    /**
     * @param $cachePath
     * @param $result
     */
    public function setCachedResponse($cachePath, $result)
    {
        $this->store("$cachePath:json", $result);
    }

    /**
     * @param $cachePath
     * @return bool|int
     */
    public function checkKeyExist($cachePath)
    {
        return $this->redis->exists("$cachePath:json");
    }

    public function cachePathGenerator($uri, $routeName, $user)
    {


        $url = ltrim($uri, "/");
        $path = parse_url($url, PHP_URL_PATH);
        $queryString = parse_url($url, PHP_URL_QUERY);

        $pathExp = explode("/",$path);
        $entity = $pathExp[0];
        $path = implode(":",$pathExp);
        $path = ($queryString) ? "{$path}:{$queryString}" : "{$path}" ;
        $cachePath = $path;
        if (count($pathExp) < 2 ){
            $cachePath = "{$entity}:all:$path";
        } else {
            $cachePath = "{$user->getId()}:{$cachePath}";
        }

       if(in_array($routeName, ["profile", "profile_end_user" ,"authentication_checker" , "bulk_sync","high_level_subject_vne","api_get_index_news_markets_lists","segment_vne", "s3_sub_folder", "segment_vne_update", "source_video_delete", "source_videos_pre_signed", "source_videos_cc_upload_presign", "story_cc_upload_presign", "story_delete", "stories_pre_signed", "story_type_vne", "story_type_vne_update", "vod_upload_presign", "vod_retranscoding", "vod_upload", "vod_initialize", "vod_transcoding_queue_update", "vod_pre_signed", "","end_user_register"])){
            $cachePath = "{$user->getId()}:{$cachePath}";
       }
        return $cachePath;
    }

    public function cachePurgeByUri($userId,$uri)
    {
        $url = ltrim($uri, "/");
        $path = parse_url($url, PHP_URL_PATH);
        $queryString = parse_url($url, PHP_URL_QUERY);

        $pathExp = explode("/",$path);
        $entity = $pathExp[0];
        $path = implode(":",$pathExp);
        $path = ($queryString) ? "{$path}:{$queryString}" : "{$path}" ;

        $this->purgeAll("{$entity}:all:*");
        $this->purgeAll("{$userId}:$path:*");
    }

}
