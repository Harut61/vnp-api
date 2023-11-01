<?php
namespace App\Services;

use App\Entity\Story;
use App\Entity\Vod;
use App\Enums\StoryStatusEnum;
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
 * Class StoryService
 * @package App\Services
 */
class StoryService
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
        $this->repository = $entityManager->getRepository(Story::class);
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
     * @return null|Story|Object
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param Story $story
     * @return Story
     */
    public function save(Story $story)
    {
        $this->entityManager->persist($story);
        $this->entityManager->flush();
        return $story;
    }

    /**
     * Send Alert to admin users on invalid media info of uploaded file or invalid file
     * @param Story $story
     * @param $mediaInfo
     */
    public function sendInvalidMediaInfoAlert(Story $story, $mediaInfo)
    {
        $vod  = $story->vod;
        $vod->mediaInfo = $mediaInfo;
        $vod->status = VodStatusEnum::PROCESSING_FAILED;
        $this->vodService->save($vod);
        $this->sendMediaInfoErrorEmail($story);
    }


    /**
     * Send Alert to admin users on transcoding failed of uploaded file or invalid file
     * @param Story $story
     * @param $vodStatus
     * @return bool|int
     */
    public function sendFailedAlert(Story $story, $vodStatus)
    {
        return $this->emailService->sendEmail("emails/story_transcoding_error.html.twig",
            getenv("ADMIN_ALERT_EMAIL"),
            [
                'story' => $story,
                'vodStatus' => $vodStatus
            ], $this->translator->trans('email.story_transcoding_error.subject'). " " .$vodStatus);
    }

    /**
     * @param Story $story
     */
    public function sendForTranscoding(Story $story){
        $queueUrl = $this->awsSqsUtil->getQueueUrl(getenv("AWS_TRANSCODING_SQS_QUEUE_NAME"));

        $message =   json_encode([
            "vodId"=>$story->vod->getId(),
            "type" => "story"
        ]);
        $messageGroupId = "story-id-{$story->getId()}-story-id-{$story->vod->getId()}". uniqid();

        if(getenv("AWS_TRANSCODING_SQS_QUEUE_TYPE") === "fifo") {
            $this->awsSqsUtil->sendMessageFifo($queueUrl , $message , $messageGroupId);
        } else {
            $this->awsSqsUtil->sendMessage($queueUrl , $message);
        }
    }


}
