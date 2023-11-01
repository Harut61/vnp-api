<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\AdminUser;
use App\Entity\SourceVideo;
use App\Entity\Story;
use App\Entity\Vod;
use App\Enums\SourceVideoStatusEnum;
use App\Services\StoryService;
use App\Services\VodService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class StoryPostSubscriber implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var StoryService
     */
    private $storyService;


    public function __construct(ContainerInterface $container, StoryService $storyService)
    {
        $this->container = $container;
        $this->storyService = $storyService;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [ 'storySendForTranscoding', EventPriorities::POST_WRITE ]
        ];
    }

    /**
     * @param ViewEvent $event
     *  Update password.
     */
    public function storySendForTranscoding(ViewEvent $event)
    {
        $object = $event->getControllerResult();
        $postParams = json_decode($event->getRequest()->getContent(), true);

        /** @var $object Story */
        if ($object instanceof Story && $postParams && empty($object->vod)) {
            /** @var EntityManagerInterface $em */
            $em = $this->container->get('doctrine')->getManager();
            /** @var Vod $vod */
            $vod = $object->sourceVideo->vod;

            $storyVod = new Vod();
            $storyVod->sourceVideo = null;
            $storyVod->story = $object;
            $storyVod->title = $object->title;
            $storyVod->originalFileName = $vod->originalFileName;
            $storyVod->originalExtension = $vod->originalExtension;
            $storyVod->originalFilePath = $vod->originalFileMp4Url;
//            $storyVod->videoPath = $vod->videoPath;
            $em->persist($storyVod);
            $object->vod = $storyVod;
            $em->persist($object);
            $em->flush();

            $this->storyService->sendForTranscoding($object);
        }
    }
}
