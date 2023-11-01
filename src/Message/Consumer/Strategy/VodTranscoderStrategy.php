<?php

namespace App\Message\Consumer\Strategy;

use App\Entity\Vod;
use App\Enums\SourceUploadTypeEnum;
use App\Model\Message;
use App\Services\Aws\AwsMediaConvertService;
use App\Services\Handlers\AppSettingHandlers;
use App\Services\TranscodingQueueService;
use App\Services\VodService;
use App\Util\AwsS3Util;
use App\Util\AwsSqsUtilInterface;
use Psr\Log\LoggerInterface;

class VodTranscoderStrategy implements StrategyInterface
{
    public const QUEUE_NAME = 'vod-transcoder';

    private $awsSqsUtil;
    private $logger;

    /** @var VodService $vodService */
    private $vodService;

    /** @var AwsMediaConvertService $awsMediaConvertService */
    private $awsMediaConvertService;

    /** @var TranscodingQueueService $transcodingQueueService */
    private $transcodingQueueService;

    /** @var AwsS3Util $awsS3Util */
    private $awsS3Util;

    /** @var AppSettingHandlers $appSettingHandlers */
    private $appSettingHandlers;

    /**
     * VodTranscoderStrategy constructor.
     * @param AwsSqsUtilInterface $awsSqsUtil
     * @param LoggerInterface $logger
     * @param VodService $vodService
     * @param AwsMediaConvertService $awsMediaConvertService
     * @param TranscodingQueueService $transcodingQueueService
     * @param AwsS3Util $awsS3Util
     * @param AppSettingHandlers $appSettingHandlers
     */
    public function __construct(
        AwsSqsUtilInterface $awsSqsUtil,
        LoggerInterface $logger,
        VodService $vodService,
        AwsMediaConvertService $awsMediaConvertService,
        TranscodingQueueService $transcodingQueueService,
        AwsS3Util $awsS3Util,
        AppSettingHandlers $appSettingHandlers
    )
    {
        $this->awsSqsUtil = $awsSqsUtil;
        $this->logger = $logger;
        $this->vodService = $vodService;
        $this->awsMediaConvertService = $awsMediaConvertService;
        $this->transcodingQueueService = $transcodingQueueService;
        $this->awsS3Util = $awsS3Util;
        $this->appSettingHandlers = $appSettingHandlers;
    }

    public function canProcess(string $queue): bool
    {
        return self::QUEUE_NAME === $queue;
    }

    public function process(Message $message): void
    {
        try {

            $transcodingDone = $this->transcodingQueueService->transcodingFinishedPerDay();
            $allowed = $this->appSettingHandlers->getAllowedTranscodingPerDay();


            if($transcodingDone >= $allowed){
                $this->echoMsg("Daily Limit reached");
                $this->logger->error("Daily Limit reached");

                $this->transcodingQueueService->limitExideEmail();
                $this->awsSqsUtil->requeueMessage($message);
                return;
            }

            $transcodingInQueue = $this->transcodingQueueService->transcodingInQueue();
            $allowed = $this->appSettingHandlers->getAllowedTranscodingParallel();

            if($transcodingInQueue >= $allowed){
                $this->echoMsg("Parallel Processing limit reached send back to queue");

                $this->awsSqsUtil->requeueMessage($message);
                return;
            }

            $body = json_decode($message->body, true);
            $this->logger->info(sprintf('The message "%s" has been consumed.', $message->id));
            $this->echoMsg("PROCESS START  for VOD {$body["vodId"]}");
            /** @var Vod $vod */
            $vod = $this->vodService->get($body["vodId"]);

            // send vod to transcoding
            $jobName = "{$body["type"]}-vod-id-{$vod->getId()}";
            $extractCaption = false;
            $clipping = false;
            $inputUrl = "";

            /**
             * Check if vod related to source video then extract close captions
             */
            if(!empty($vod->sourceVideo)) {
                $extractCaption = true;
                $this->awsS3Util->setInTackerClient();
                $bucketName = $this->awsS3Util->getBucketName($vod->originalFileBucket);
                $inputUrl = $this->awsS3Util->getPreSignedUrl($bucketName, $vod->originalFilePath, "GetObject", 3600);

            }

            if(!empty($vod->story)) {
                $bucketName = $_ENV["AWS_S3_TRANSCODING_OUTPUT_BUCKET"];
                
                if($vod->story->sourceVideo->uploadedType == SourceUploadTypeEnum::NAS) {
                    $bucketName = $_ENV["NAS_SYNC_BUCKET"];
                } else {
                    $bucketName = $this->awsS3Util->getBucketName($bucketName);
                }

                $clipping = true;
                $this->awsS3Util->setS3Client($_ENV["SOURCE_VIDEO_CDN_URL"]);
               
                $originalFilePath = $vod->originalFilePath;
                if(empty($originalFilePath)) {
                    $originalFilePath = $vod->story->sourceVideo->vod->originalFilePath;
                }
                $this->echoMsg("========================= SourceVideo originalFilePath ===============================");
                var_dump($originalFilePath);
                $this->echoMsg("========================= SourceVideo originalFilePath ===============================");

                $inputUrl = $this->awsS3Util->getS3Url($bucketName, $originalFilePath);
            }

            if(!empty($vod->interstitial)) {
                $clipping = false;
                $this->awsS3Util->setInTackerClient();
                $bucketName = $this->awsS3Util->getBucketName($vod->originalFileBucket);
                $inputUrl = $this->awsS3Util->getPreSignedUrl($bucketName, $vod->originalFilePath, "GetObject", 3600);

            }

            $this->echoMsg("Transcoding Queue Initializing.. ");
            $transcodingQueue = $this->transcodingQueueService->init($vod);
            
            $this->echoMsg("========================= Create Job Params ===============================");
            var_dump($_ENV["SOURCE_VIDEO_CDN_URL"]);
            var_dump($jobName);
            var_dump($inputUrl);
            var_dump($extractCaption);
            var_dump($clipping);
            $this->echoMsg("========================= Create Job Params ===============================");

            $jobDesc = $this->awsMediaConvertService->createJob($jobName, $inputUrl, $extractCaption, $clipping , $vod);
            $jobDesc = $jobDesc->toArray();
            $this->echoMsg("Transcoding Job Created=== {$jobDesc["Job"]["Id"]} ");

            $this->echoMsg("Updating Transcoding Queue {$transcodingQueue->getId()} with Job Id");
            $this->transcodingQueueService->saveJobInfo($transcodingQueue, $jobDesc);

            $this->echoMsg("Deleting message from sqs");
            $this->awsSqsUtil->deleteMessage($message);
            $this->echoMsg("PROCESS END for VOD {$vod->getId()}");

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
}
