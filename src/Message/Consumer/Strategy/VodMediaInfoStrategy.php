<?php
namespace App\Message\Consumer\Strategy;


use App\Entity\SourceVideo;
use App\Entity\Vod;
use App\Enums\TranscodingContentTypeEnum;
use App\Enums\VodStatusEnum;
use App\Model\Message;
use App\Services\EmailService;
use App\Services\InterstitialService;
use App\Services\SourceVideoService;
use App\Services\VodService;
use App\Util\AwsS3Util;
use App\Util\AwsSqsUtilInterface;
use Psr\Log\LoggerInterface;

/**
 * Get MediaInfo from uploaded video to Bucket and save mediainfo for vod
 * and resend video for transcoding if it is needed
 *
 * Class VodMediaInfoStrategy
 * @package App\Message\Consumer\Strategy
 */
class VodMediaInfoStrategy implements StrategyInterface
{
    public const QUEUE_NAME = 'vod-mediainfo';

    private $awsSqsUtil;
    private $logger;
    private $vodService;
    private $awsS3Util;
    private $sourceVideoService;
    private $interstitialService;

    /**
     * VodMediaInfoStrategy constructor.
     * @param AwsSqsUtilInterface $awsSqsUtil
     * @param LoggerInterface $logger
     * @param VodService $vodService
     * @param SourceVideoService $sourceVideoService
     * @param InterstitialService $interstitialService
     * @param AwsS3Util $awsS3Util
     */
    public function __construct(
        AwsSqsUtilInterface $awsSqsUtil,
        LoggerInterface $logger,
        VodService $vodService,
        SourceVideoService $sourceVideoService,
        InterstitialService $interstitialService,
        AwsS3Util $awsS3Util

    ) {
        $this->awsSqsUtil = $awsSqsUtil;
        $this->logger = $logger;
        $this->vodService = $vodService;
        $this->sourceVideoService = $sourceVideoService;
        $this->interstitialService = $interstitialService;
        $this->awsS3Util = $awsS3Util;
    }

    public function canProcess(string $queue): bool
    {
        return self::QUEUE_NAME === $queue;
    }

    public function process(Message $message): void
    {
        try {
            $body = json_decode($message->body, true);
            echo sprintf('The message "%s" received.', $message->id)."\n";
            echo "##########################\n";
            echo json_encode($body)."\n";
            echo "##########################\n";
            $this->logger->info(sprintf('The message "%s" received id = ', $message->id));
            $vod = $this->vodService->get($body["vodId"]);

            $bucket = $this->awsS3Util->getBucketName($body["bucket"]);
            $this->awsS3Util->setInTackerClient();
            $signedUrl = $this->awsS3Util->getPreSignedUrl($bucket, $body["originalFilePath"],'GetObject',100);

            $mediaInfo = [];
            try{
                $mediaInfo = $this->vodService->getMediaInfo($signedUrl);
                echo "====MediaInfo Found =======\n";
                echo json_encode($mediaInfo);
                echo "====MediaInfo Found =======\n";
            }catch (\Exception $exception){
                echo "====[Error] invalid MediaInfo ="; echo $exception->getMessage()." ======\n";

                $this->sourceVideoService->sendInvalidMediaInfoAlert($vod->sourceVideo, $mediaInfo);
                $this->awsSqsUtil->deleteMessage($message);

            }

            if(!array_key_exists("videos",$mediaInfo) || empty($mediaInfo["videos"])){
                echo "==== invalid file ="; echo $vod->getId()." ======\n";
                $this->sourceVideoService->sendInvalidMediaInfoAlert($vod->sourceVideo, $mediaInfo);
                echo "==== InvalidMediaInfoAlert Sent======";
            }else{
               

                if($body["contentType"] == TranscodingContentTypeEnum::SOURCE_VIDEO && !empty($vod)) {
                    echo "==== vod Updated with MEDIA INFO="; echo $vod->getId()."======\n";
                    $vod = $this->vodService->create($vod, $mediaInfo);
                    
                    echo "==== vod type = SOURCE VIDEO"."======\n";
                    $this->sourceVideoService->sendForTranscoding($vod->sourceVideo);
                }

                if($body["contentType"] == TranscodingContentTypeEnum::INTERSTITIAL && !empty($vod)) {
                    echo "==== vod type = INTERSTITIAL"."======\n";
                    $this->interstitialService->sendForTranscoding($vod->interstitial);
                }
            }

            echo "==== deleting message from queue =======\n";
            $this->awsSqsUtil->deleteMessage($message);
            echo sprintf('The message "%s" has been consumed.', $message->id)."\n";
            $this->logger->info(sprintf('The message "%s" has been consumed.', $message->id));

        } catch (\Exception $exception){

            // Todo send message to sentry
            echo "==== Exception ". $exception->getMessage()."======\n";
            $this->awsSqsUtil->requeueMessage($message);
            $this->logger->alert(sprintf('The message "%s" has been put in the "flight" mode.', $message->id));
        }

    }
}