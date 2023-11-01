<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\AdminUser;
use App\Entity\Vod;
use App\Enums\SourceVideoStatusEnum;
use App\Enums\StoryStatusEnum;
use App\Enums\VodStatusEnum;
use App\Services\SourceVideoService;
use App\Services\StoryService;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class VodSubscriber implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var SourceVideoService
     */
    private $sourceVideoService;

    /**
     * @var StoryService
     */
    private $storyService;

    public function __construct(ContainerInterface $container, SourceVideoService $sourceVideoService, StoryService $storyService)
    {
        $this->container = $container;
        $this->sourceVideoService = $sourceVideoService;
        $this->storyService = $storyService;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [ 'vodStatusUpdate', EventPriorities::PRE_WRITE ],
            Events::prePersist => 'vodStatusUpdate'
        ];
    }

    /**
     * @param ViewEvent $event
     *  Update password.
     */
    public function vodStatusUpdate(ViewEvent $event)
    {
        $object = $event->getControllerResult();
        /** $object Vod */
        if ($object instanceof Vod && property_exists($object,"status")) {

            if($object->sourceVideo){

                $sourceVideo = $object->sourceVideo;

                if($object->status == VodStatusEnum::READY) {

                    $sourceVideo->status = SourceVideoStatusEnum::READY_FOR_MARKER;
                    $sourceVideo->uploadedAt = new \DateTime();
                } elseif (in_array($object->status , [
                    VodStatusEnum::TRANSCODING_IN_PROGRESS,
                    VodStatusEnum::TRANSCODING_IN_A_QUEUE
                ])){
                    $sourceVideo->status = SourceVideoStatusEnum::PROCESSING;
                    $sourceVideo->uploadedAt = new \DateTime();
                } elseif (in_array($object->status , [
                    VodStatusEnum::PROCESSING_FAILED,
                    VodStatusEnum::ERROR_IN_TRANSCODING,
                    VodStatusEnum::TRANSCODING_CANCELED,
                ])) {

                    $sourceVideo->status = SourceVideoStatusEnum::PROCESSING_FAILED;
                    $sourceVideo->uploadedAt = new \DateTime();
                    $this->sourceVideoService->sendFailedAlert($sourceVideo , $object->status);

                }
                $this->sourceVideoService->save($sourceVideo);
            } else if($object->story){

                $story = $object->story;

                if($object->status == VodStatusEnum::READY) {

                    $story->storyStatus = StoryStatusEnum::GENERATED;

                } elseif (in_array($object->status , [
                    VodStatusEnum::TRANSCODING_IN_PROGRESS,
                    VodStatusEnum::TRANSCODING_IN_A_QUEUE
                ])){
                    $story->storyStatus = StoryStatusEnum::QUEUED;
                } elseif (in_array($object->status , [
                    VodStatusEnum::PROCESSING_FAILED,
                    VodStatusEnum::ERROR_IN_TRANSCODING,
                    VodStatusEnum::TRANSCODING_CANCELED,
                ])) {

                    $story->storyStatus = StoryStatusEnum::STORY_GENERATION_FAILED;
                    $this->storyService->sendFailedAlert($story , $object->status);

                }
                $this->storyService->save($story);
            }
        }
    }
}
