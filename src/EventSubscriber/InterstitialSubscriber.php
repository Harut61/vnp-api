<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\AdminUser;
use App\Entity\Interstitial;
use App\Enums\VodStatusEnum;
use App\Services\VodService;
use App\Util\AwsSqsUtil;
use Chrisyue\PhpM3u8\Facade\ParserFacade;
use Chrisyue\PhpM3u8\Stream\TextStream;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class InterstitialSubscriber implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var AwsSqsUtil
     */
    private  $awsSqsUtil;

    /**
     * @var VodService
     */
    private  $vodService;

    public function __construct(ContainerInterface $container, AwsSqsUtil $awsSqsUtil, VodService $vodService)
    {
        $this->container = $container;
        $this->awsSqsUtil = $awsSqsUtil;
        $this->vodService = $vodService;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [ 'setM3u8Config', EventPriorities::PRE_WRITE ],
            Events::prePersist => [ 'setM3u8Config', Events::prePersist ],
        ];
    }

    /**
     * @param ViewEvent $event
     */
    public function setM3u8Config(ViewEvent $event)
    {
        /** @var Interstitial $object */
        $object = $event->getControllerResult();
        if ($object instanceof Interstitial && in_array($event->getRequest()->getMethod(), ["POST", "PATCH"])) {
            /** @var EntityManagerInterface $em */
            $em = $this->container->get('doctrine')->getManager();
            if($object->vod){
                if($object->vod->status == VodStatusEnum::READY) {
                    $response = $this->vodService->getPreSignedUrl($object->vod, getenv("VOD_BUCKET"));
                    if($response["url"]){
                        $mediaPlaylist = $this->parseM3u8($response["url"]);

                        if(array_key_exists("EXT-X-STREAM-INF", $mediaPlaylist)){
                            $newFileName = $mediaPlaylist["EXT-X-STREAM-INF"][0]["uri"];
                            $oldFileName = basename($response["url"]);
                            $url = str_replace($oldFileName, $newFileName, $response["url"]);
                            $mediaPlaylist = $this->parseM3u8($url);
                            $object = $this->setLastChunkInfo($mediaPlaylist["mediaSegments"], $object);
                        } else {
                            $object = $this->setLastChunkInfo($mediaPlaylist["mediaSegments"], $object);
                        }
                    }

                    $em->persist($object);
                }
                $em->flush();
            }

        }

    }

      /**
     * parse m3u8 playlist to array
     *
     * @param string $url
     * @return array|mix
     */
    public function parseM3u8($url)
    {
        $parser = new ParserFacade();
        $content =   file_get_contents($url);
          /**
         * @var ArrayObject
         */
        return $parser->parse(new TextStream($content));
    }

    /**
     * @param $mediaPlaylist
     * @param Interstitial $interstitial
     * @return mixed
     */
    public function setLastChunkInfo($mediaPlaylist, Interstitial $interstitial)
    {
        $mediaSegments = $mediaPlaylist;
        $lastChumkNumber = count($mediaSegments);
        $lastChunk = end($mediaSegments);
            
        $duration = $lastChunk["EXTINF"]->getDuration();

        $interstitial->lastChunkDuration = $duration;
        $interstitial->lastChunkNumber = $lastChumkNumber;
        return $interstitial;
    }
    
}
