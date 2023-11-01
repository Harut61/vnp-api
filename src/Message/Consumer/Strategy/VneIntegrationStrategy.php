<?php

namespace App\Message\Consumer\Strategy;


use App\Entity\HighLevelSubjectTag;
use App\Entity\SourceVideo;
use App\Entity\Story;
use App\Enums\SourceVideoStatusEnum;
use App\Enums\VodStatusEnum;
use App\Exception\GrpcException;
use App\Model\Message;
use App\Services\Aws\AwsMediaConvertService;
use App\Services\Handlers\AppSettingHandlers;
use App\Services\TranscodingQueueService;
use App\Services\Vne\BaseService;
use App\Services\Vne\SourceVideoService;
use App\Services\VneService;
use App\Services\VodService;
use App\Util\AwsS3Util;
use App\Util\AwsSqsUtil;
use App\Util\AwsSqsUtilInterface;
use Doctrine\ORM\EntityManagerInterface;
use Endpoints\Events\AddStoryReply;
use Endpoints\Events\AddStoryRequest;
use Psr\Log\LoggerInterface;
use Endpoints\Events\EventsClient;


class VneIntegrationStrategy implements StrategyInterface
{
    public const QUEUE_NAME = "vnp-vne-integration";
    /** @var AwsSqsUtil */
    private $awsSqsUtil;
    private $logger;

    /** @var VneService $vneService */
    private $vneService;


    /** @var AwsS3Util $awsS3Util */
    private $awsS3Util;

    /** @var EntityManagerInterface $em */
    private $em;

    /** @var  SourceVideoService */
    private $sourceVideoService;

    /**
     * @var $requestType string
     */
    public $requestType;

    /**
     * @var EventsClient
     */
    public $client;

    /**
     * VneIntegrationStrategy constructor.
     * @param AwsSqsUtilInterface $awsSqsUtil
     * @param LoggerInterface $logger
     * @param VneService $vneService
     * @param AwsS3Util $awsS3Util
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        AwsSqsUtilInterface $awsSqsUtil,
        LoggerInterface $logger,
        VneService $vneService,
        AwsS3Util $awsS3Util,
        EntityManagerInterface $entityManager,
        SourceVideoService $sourceVideoService
    )
    {
        $this->awsSqsUtil = $awsSqsUtil;
        $this->logger = $logger;
        $this->awsS3Util = $awsS3Util;
        $this->em = $entityManager;
        $this->sourceVideoService = $sourceVideoService;
        $this->vneService = $vneService;
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
            $vneService = "";
           
            if($body["type"] == "Source") {
                $this->echoMsg("Content Type  Source Found");
                $vneService = "SourceService";
            }

            if($body["type"] == "Story") {
                $this->echoMsg("Content Type  Story Found");
                $vneService = "StoryService";
            }

            if($body["type"] == "Show") {
                $this->echoMsg("Content Type  Show Found");
                $vneService = "ShowService";
            }


            if($body["type"] == "SourceVideo") {
                $this->echoMsg("Content Type  SourceVideo Found");
                $vneService = "SourceVideoService";
            }

            if($body["type"] == "EndUser") {
                $this->echoMsg("Content Type  EndUser Found");
                $vneService = "EndUserService";
            }

            if($body["type"] == "LineUp") {
                $this->echoMsg("Content Type  LineUp Found");
                $vneService = "LineupService";
            }

            $service = $this->vneService->loadService("$vneService");

            if($body["method"] == "POST"){
                
                try {
                    
                    $service = $this->vneService->service;
                    $response = $service->post($body["id"]);
                    $this->echoMsg("Response ");
                    var_dump(json_encode($response));

                } catch (\Exception $exception) {
                    var_dump($exception->getMessage());
                }

                
            }

            if(in_array($body["method"], ["PATCH", "PUT"])){
                try { 
                    $service = $this->vneService->service;
                    $response = $service->put($body["id"]);
                    $this->echoMsg("Response ");
                    var_dump(json_encode($response));
                } catch (\Exception $exception) {
                    var_dump($exception->getMessage());
                }
            }

            if($body["type"] == "SourceVideo" && $body["method"] == "POST"){
                try {
                 
                    /** @var SourceVideo $sourceVideo */
                    $sourceVideo = $this->em->getRepository(SourceVideo::class)->findOneBy(['id' => $body["id"]]);

                    if($sourceVideo->status == SourceVideoStatusEnum::READY_FOR_MARKER){
                        $queueUrl = $this->awsSqsUtil->getQueueUrlFromFullName(getenv("VNE_AUDIO_LINK_SQS_QUEUE_NAME"));
                        $this->awsSqsUtil->sendMessageFifo($queueUrl,
                            json_encode([
                                "source_video_id" => $sourceVideo->getId(),
                                "show_vnp_id" => $sourceVideo->show->getId(),
                                "show_publication_datetime" => $sourceVideo->publicationDate,
                                "FPS" => $sourceVideo->vod->videoFps,
                                "Show_duration" => $sourceVideo->vod->duration,
                                "Audio_legth" => $sourceVideo->vod->audioCodec,
                                "Video_length" => $sourceVideo->vod->videoWidth
                            ]),
                            "source-video-id-{$sourceVideo->getId()}-" . uniqid()
                        );
                    }    
                  
                } catch (\Exception $exception) {
                    var_dump($exception->getMessage());
                }

            }

            if($body["type"] == "SourceVideo" && in_array($body["method"], ["PATCH", "PUT"])){

                try{
                        /** @var SourceVideo $sourceVideo */
                        $sourceVideo = $this->em->getRepository(SourceVideo::class)->findOneBy(['id' => $body["id"]]);
                        if($sourceVideo->storyStatus == SourceVideoStatusEnum::READY_FOR_MARKER) {
                            $queueUrl = $this->awsSqsUtil->getQueueUrlFromFullName(getenv("VNE_AUDIO_LINK_SQS_QUEUE_NAME"));
                            $this->awsSqsUtil->sendMessageFifo($queueUrl,
                                json_encode([
                                    "source_video_id" => $sourceVideo->getId(),
                                    "show_vnp_id" => $sourceVideo->show->getId(),
                                    "show_publication_datetime" => $sourceVideo->publicationDate,
                                    "FPS" => $sourceVideo->vod->videoFps,
                                    "Show_duration" => $sourceVideo->vod->duration,
                                    "Audio_legth" => $sourceVideo->vod->audioCodec,
                                    "Video_length" => $sourceVideo->vod->videoWidth
                                ]),
                                "source-video-id-{$sourceVideo->getId()}-" . uniqid()
                            );
                        }
                        
                }catch(\Exception $exception){
                    var_dump($exception->getMessage());
                }
                

            }

            $this->awsSqsUtil->deleteMessage($message);

        } catch (\Exception $exception) {

            // Todo send message to sentry
            echo "==== Exception " . $exception->getMessage() . "======\n";

            if(strpos($exception->getMessage(), "already exist") !== false){
                $this->awsSqsUtil->deleteMessage($message);
            } else{
                $this->awsSqsUtil->requeueMessage($message);
            }
            
            $this->logger->alert(sprintf('The message "%s" has been put in the "flight" mode.', $message->id));
        }

    }

    private function echoMsg($msg){
        echo "========== $msg =========\n";
    }

    private function handleErrorResponse(array $response): void
    {
        if ($response[1]->code !== 0) {
            throw new GrpcException(
                sprintf(
                    'gRPC request failed : error code: %s, details: %s',
                    $response[1]->code,
                    $response[1]->details
                )
            );
        }
    }

}