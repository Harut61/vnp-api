<?php

namespace App\Message\Consumer\Strategy;


use App\Entity\SourceVideo;
use App\Entity\Story;
use App\Model\Message;
use App\Services\Aws\AwsMediaConvertService;
use App\Services\Handlers\AppSettingHandlers;
use App\Services\SourceVideoService;
use App\Services\StoryService;
use App\Services\TranscodingQueueService;
use App\Services\VodService;
use App\Util\AwsS3Util;
use App\Util\AwsSqsUtilInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class SourceVideoDeleteStrategy implements StrategyInterface
{
    public const QUEUE_NAME = "source-video-delete";

    private $awsSqsUtil;
    private $logger;

    /** @var SourceVideoService $sourceVideoService */
    private $sourceVideoService;

    /** @var StoryService $storyService */
    private $storyService;


    /** @var AwsS3Util $awsS3Util */
    private $awsS3Util;

    /** @var EntityManagerInterface $em */
    private $em;

    /**
     * SourceVideoDeleteStrategy constructor.
     * @param AwsSqsUtilInterface $awsSqsUtil
     * @param LoggerInterface $logger
     * @param SourceVideoService $sourceVideoService
     * @param AwsS3Util $awsS3Util
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        AwsSqsUtilInterface $awsSqsUtil,
        LoggerInterface $logger,
        SourceVideoService $sourceVideoService,
        StoryService $storyService,
        AwsS3Util $awsS3Util,
        EntityManagerInterface $entityManager
    )
    {
        $this->awsSqsUtil = $awsSqsUtil;
        $this->logger = $logger;
        $this->sourceVideoService = $sourceVideoService;
        $this->storyService = $storyService;
        $this->awsS3Util = $awsS3Util;
        $this->em = $entityManager;
    }

    /**
     * @param string $queue
     * @return bool
     */
    public function canProcess(string $queue): bool
    {
        return self::QUEUE_NAME === $queue;
    }

    public function process(Message $message): void
    {
        try {

            $body = json_decode($message->body, true);
            $this->logger->info(sprintf('The message "%s" has been consumed.', $message->id));
           
            $deleteType = "";
            if ( array_key_exists("sourceVideoId", $body) ) {
                $deleteType = "sourceVideo";
                $id = $body["sourceVideoId"];
            }  else if ( array_key_exists("storyId", $body) ) {
                $deleteType = "story";
                $id = $body["storyId"];
            }
            $this->echoMsg("DELETE PROCESS START  for $deleteType $id");
           

            if( $deleteType === "sourceVideo") {
                /** @var SourceVideo $sourceVideo */
                $sourceVideo = $this->sourceVideoService->get($body["sourceVideoId"]);
                $this->deleteSourceVideo($sourceVideo);
            } else  if( $deleteType === "story") {
                /** @var SourceVideo $sourceVideo */
                $story = $this->storyService->get($body["storyId"]);
                $this->deleteStory($story);
            }


            $this->awsSqsUtil->deleteMessage($message);

        } catch (\Exception $exception) {

            // Todo send message to sentry
            echo "==== Exception " . $exception->getMessage() . "======\n";
            $this->awsSqsUtil->requeueMessage($message);
            $this->logger->alert(sprintf('The message "%s" has been put in the "flight" mode.', $message->id));
        }

    }

    private function echoMsg($msg){
        echo "========== $msg =========\n";
    }
    
    /** 
     *  @var SourceVideo $sourceVideo
    */
    private function deleteSourceVideo(SourceVideo $sourceVideo)
    {
        if($sourceVideo)
            {
                $bucket = $this->awsS3Util->getBucketName(getenv("AWS_S3_TRANSCODING_OUTPUT_BUCKET"));
                $this->echoMsg("Deleting Source Video {$sourceVideo->getId()} ");
                $this->awsS3Util->setS3Client();
                if(!empty($sourceVideo->vod)){
                    $this->awsS3Util->deleteFolder($bucket, $sourceVideo->vod->getId());
                }

                $this->echoMsg("Source Video {$sourceVideo->getId()} Deleted SuccessFully.. ");

                $stories = $sourceVideo->stories;
                if(!empty($stories)){
                    /** @var Story $story */
                    foreach ($stories as $story) {
                        $this->echoMsg("Deleting Story {$story->getId()}");
                        $this->awsS3Util->setWasabiClient("https://s3.wasabisys.com");
                        $storyBucket = $this->awsS3Util->getBucketName(getenv("VOD_BUCKET"));

                        if(!empty($story->vod)) {
                            $this->awsS3Util->deleteFolder($storyBucket, $story->vod->getId());
                        }

                        $story->setDeletedAt(new \Datetime());
                        $this->em->persist($story);
                        $this->echoMsg("Story {$story->getId()} Deleted SuccessFully.. ");
                    }
                }
                $sourceVideo->setDeletedAt(new \Datetime());
                $sourceVideo->setSlug($sourceVideo->getSlug()."-".md5(time()));
                $this->em->persist($sourceVideo);
                $this->em->flush();
            }

    }   
    /**
     * 
     *
     * @param Story $story
     * @return void
     */
    private  function deleteStory(Story $story)
    {
        $this->echoMsg("Deleting Story {$story->getId()}");
        $this->awsS3Util->setWasabiClient("https://s3.wasabisys.com");
        $storyBucket = $this->awsS3Util->getBucketName(getenv("VOD_BUCKET"));

        if(!empty($story->vod)) {
            $this->awsS3Util->deleteFolder($storyBucket, $story->vod->getId());
        }

        $story->setDeletedAt(new \Datetime());
        $this->em->persist($story);
        $this->echoMsg("Story {$story->getId()} Deleted SuccessFully.. ");
        $this->em->flush();
    }
}