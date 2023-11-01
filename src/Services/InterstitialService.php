<?php
namespace App\Services;

use App\Entity\Interstitial;
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
class InterstitialService
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
        $this->repository = $entityManager->getRepository(Interstitial::class);
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
     * @return null|Interstitial|Object
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param Vod $vod
     * @return Interstitial
     */
    public function init(Vod $vod){
        $intestitial = new Interstitial();
        $intestitial->vod = $vod;
        return $this->save($intestitial);
    }


    /**
     * @param Interstitial $interstitial
     * @return Interstitial
     */
    public function save(Interstitial $interstitial)
    {
        $this->entityManager->persist($interstitial);
        $this->entityManager->flush();
        return $interstitial;
    }

   /**
    * Send Alert to admin users on invalid media info of uploaded file or invalid file
    * @param Interstitial $interstitial
    * @param $mediaInfo
    */
   public function sendInvalidMediaInfoAlert(Interstitial $interstitial, $mediaInfo)
   {
       $vod  = $interstitial->vod;
       $vod->mediaInfo = $mediaInfo;
       $vod->status = VodStatusEnum::PROCESSING_FAILED;
       $this->vodService->save($vod);
       
   }


   /**
    * Send Alert to admin users on transcoding failed of uploaded file or invalid file
    * @param Interstitial $interstitial
    * @param $vodStatus
    * @return bool|int
    */
   public function sendFailedAlert(Interstitial $interstitial, $vodStatus)
   {
       return $this->emailService->sendEmail("emails/interstitial_transcoding_error.html.twig",
           getenv("ADMIN_ALERT_EMAIL"),
           [
               'interstitial' => $interstitial,
               'vodStatus' => $vodStatus
           ], $this->translator->trans('email.source_video_error.subject'). " " .$vodStatus);
   }

   /**
    * @param Interstitial $interstitial
    */
   public function sendForTranscoding(Interstitial $interstitial){
       $queueUrl = $this->awsSqsUtil->getQueueUrl(getenv("AWS_TRANSCODING_SQS_QUEUE_NAME"));

       $message =   json_encode([
           "vodId"=>$interstitial->vod->getId(),
           "type" => "interstitial"
       ]);
       $messageGroupId = "interstitial-id-{$interstitial->getId()}-interstitial-id-{$interstitial->vod->getId()}". uniqid();

       if(getenv("AWS_TRANSCODING_SQS_QUEUE_TYPE") === "fifo") {
           $this->awsSqsUtil->sendMessageFifo($queueUrl , $message , $messageGroupId);
       } else {
           $this->awsSqsUtil->sendMessage($queueUrl , $message);
       }
   }


}
