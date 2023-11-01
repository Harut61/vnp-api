<?php
namespace App\Services;

use App\Entity\SourceVideo;
use App\Entity\Vod;
use App\Enums\SourceVideoStatusEnum;
use App\Enums\VodStatusEnum;
use App\Services\Aws\SqsService;
use App\Util\AwsSqsUtil;
use App\Util\AwsS3Util;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Mhor\MediaInfo\Type\General;
use Mhor\MediaInfo\Type\Video;
use Predis\ClientInterface;
use Snc\RedisBundle\Client\Phpredis\Client;
use Mhor\MediaInfo\MediaInfo;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SourceVideoService
 * @package App\Services
 */
class SourceVideoService
{
    /** @var $entityManager EntityManagerInterface  */
    private $entityManager;

    /** @var  $repository ObjectRepository */
    private $repository;

    /** @var  $redisClient ClientInterface */
    private $redisClient;

    /** @var  $awsSqsUtil AwsSqsUtil */
    private $awsSqsUtil;

    /** @var  $awsS3Util AwsS3Util */
    private $awsS3Util;

    /** @var  $emailService EmailService */
    private $emailService;

    /** @var  $vodService VodService */
    private $vodService;

    /** @var TranslatorInterface $translator */
    protected $translator;

    public function __construct(EntityManagerInterface $entityManager,
                                ClientInterface $redisClient,
                                AwsSqsUtil $awsSqsUtil,
                                AwsS3Util $awsS3Util,
                                EmailService $emailService,
                                VodService $vodService,
                                TranslatorInterface $translator)
    {
        $this->repository = $entityManager->getRepository(SourceVideo::class);
        $this->entityManager = $entityManager;
        $this->redisClient = $redisClient;
        $this->awsSqsUtil = $awsSqsUtil;
        $this->awsS3Util = $awsS3Util;
        $this->emailService = $emailService;
        $this->vodService = $vodService;
        $this->translator = $translator;
    }

    /**
     * @param $id
     * @return null|SourceVideo|Object
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param Vod $vod
     * @return SourceVideo
     */
    public function init(Vod $vod){
        $sourceVideo = new SourceVideo();
        $sourceVideo->vod = $vod;
        $sourceVideo->title = $vod->title;
        return $this->save($sourceVideo);
    }
    /**
     * @param SourceVideo $sourceVideo
     * @return bool|int
     */
    public function sendMediaInfoErrorEmail(SourceVideo $sourceVideo)
    {
        return $this->emailService->sendEmail("emails/source_video_error.html.twig",
            getenv("ADMIN_ALERT_EMAIL"),
            [
                'sourceVideo' => $sourceVideo
            ], $this->translator->trans('email.source_video_media_info_error.subject'));
    }

    /**
     * @param SourceVideo $sourceVideo
     * @return SourceVideo
     */
    public function save(SourceVideo $sourceVideo)
    {
        $this->entityManager->persist($sourceVideo);
        $this->entityManager->flush();
        return $sourceVideo;
    }

    /**
     * Send Alert to admin users on invalid media info of uploaded file or invalid file
     * @param SourceVideo $sourceVideo
     * @param $mediaInfo
     */
    public function sendInvalidMediaInfoAlert(SourceVideo $sourceVideo, $mediaInfo)
    {
        $vod  = $sourceVideo->vod;
        $vod->mediaInfo = $mediaInfo;
        $vod->status = VodStatusEnum::PROCESSING_FAILED;
        $this->vodService->save($vod);
        $this->sendMediaInfoErrorEmail($sourceVideo);
    }


    /**
     * Send Alert to admin users on transcoding failed of uploaded file or invalid file
     * @param SourceVideo $sourceVideo
     * @param $vodStatus
     * @return bool|int
     */
    public function sendFailedAlert(SourceVideo $sourceVideo, $vodStatus)
    {
        return $this->emailService->sendEmail("emails/source_video_error.html.twig",
            getenv("ADMIN_ALERT_EMAIL"),
            [
                'sourceVideo' => $sourceVideo,
                'vodStatus' => $vodStatus
            ], $this->translator->trans('email.source_video_error.subject'). " " .$vodStatus);
    }

    /**
     * @param SourceVideo $sourceVideo
     */
    public function sendForTranscoding(SourceVideo $sourceVideo){
        $queueUrl = $this->awsSqsUtil->getQueueUrl(getenv("AWS_TRANSCODING_SQS_QUEUE_NAME"));

        $message =   json_encode([
            "vodId"=>$sourceVideo->vod->getId(),
            "type" => "source_video"
        ]);
        $messageGroupId = "source-id-{$sourceVideo->getId()}-source-id-{$sourceVideo->vod->getId()}". uniqid();

        if(getenv("AWS_TRANSCODING_SQS_QUEUE_TYPE") === "fifo") {
            $this->awsSqsUtil->sendMessageFifo($queueUrl , $message , $messageGroupId);
        } else {
            $this->awsSqsUtil->sendMessage($queueUrl , $message);
        }
    }


}
