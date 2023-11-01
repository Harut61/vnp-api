<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\AdminUser;
use App\Entity\SourceVideo;
use App\Entity\Story;
use App\Enums\SourceVideoStatusEnum;
use App\Enums\StoryStatusEnum;
use App\Filter\NullFilter;
use App\Services\VodService;
use App\Util\AwsSqsUtil;
use Chrisyue\PhpM3u8\Facade\ParserFacade;
use Chrisyue\PhpM3u8\Stream\TextStream;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class StorySubscriber implements EventSubscriberInterface
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
            KernelEvents::VIEW => [ 'arrangeStoryCount', EventPriorities::PRE_WRITE ],
            Events::prePersist => [ 'arrangeStoryCount', Events::prePersist ],
        ];
    }

    /**
     * @param ViewEvent $event
     */
    public function arrangeStoryCount(ViewEvent $event)
    {
        /** @var Story $object */
        $object = $event->getControllerResult();
        $requestUri = $event->getRequest()->getRequestUri();
        $requestUri = explode("/", $requestUri);
        $isStoryDelete = false;
        $storyId = 0;
        if(count($requestUri) > 2 && $requestUri[1] == "stories" && $event->getRequest()->getMethod() == "DELETE") {
            $isStoryDelete = true;
            $storyId = $requestUri[2];
        }
        $isStory = false;
        if ($object instanceof Story) {
            $isStory = true;
        } else if($isStoryDelete){
            $isStory = true;
        }

        if(!$isStory){
            return;
        }
        /** @var EntityManagerInterface $em */
        $em = $this->container->get('doctrine')->getManager();
        if($isStoryDelete) {
            $object = $em->getRepository(Story::class)->find($storyId);
        }

        $em->getConnection()->beginTransaction();
        $stories = $em->getRepository(Story::class)->findBy(["sourceVideo" => $object->sourceVideo->getId(), "deletedAt" => null],["storyStartFrame"=> "ASC"]);
        $rank = 1;

        /** @var Story $story */
        foreach ($stories as $story) {
            $story->storyRank = $rank;
            $em->persist($story);
            $rank++;
        }

        /** @var SourceVideo $sourceVideo */
        $sourceVideo = $object->sourceVideo;

        if($object->storyStatus == StoryStatusEnum::GENERATED) {
            
            
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

            $entity = (new \ReflectionClass($object))->getShortName();
            $queueUrl = $this->awsSqsUtil->getQueueUrl($_ENV["VNE_INTEGRATION_SQS_QUEUE_NAME"]);

            $this->awsSqsUtil->sendMessage($queueUrl ,
                json_encode([
                    "id"=>$object->getId(),
                    "type" => $entity,
                    "method" => "POST"
                ])
            );
            $object->setReadyToViewAt(new \DateTime());
            $em->persist($object);
        }

        $sourceVideo->setStoryCount(count($stories));
        $em->persist($sourceVideo);
        $em->flush();
        $em->getConnection()->commit();
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
     * Set Story Last Chunk Info
     *
     * @param array|mix $mediaPlaylist
     * @param Story $story
     * @return Story
     */
    public function setLastChunkInfo($mediaPlaylist, Story $story)
    {
        $mediaSegments = $mediaPlaylist;
        $lastChumkNumber = count($mediaSegments);
        $lastChunk = end($mediaSegments);
            
        $duration = $lastChunk["EXTINF"]->getDuration();

        $story->lastChunkDuration = $duration;
        $story->lastChunkNumber = $lastChumkNumber;
        return $story;
    }
    
}
