<?php
namespace App\Services;

use App\Entity\SourceVideo;
use App\Entity\TranscodingQueue;
use App\Entity\Vod;
use App\Enums\TranscodingQueueStatusEnum;
use App\Enums\VodStatusEnum;
use App\Repository\TranscodingQueueRepository;
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
 * Class VodService
 * @package App\Services
 */
class TranscodingQueueService
{
    /** @var $entityManager EntityManagerInterface  */
    private $entityManager;

    /** @var  $repository TranscodingQueueRepository */
    private $repository;

    /** @var  $redisClient ClientInterface */
    private $redisClient;

    /** @var  $awsSqsUtil AwsSqsUtil */
    private $awsSqsUtil;

    /** @var  $awsS3Util AwsS3Util */
    private $awsS3Util;

    /** @var  $emailService EmailService */
    private $emailService;

    /** @var  $translator TranslatorInterface */
    private $translator;

    public function __construct(EntityManagerInterface $entityManager, ClientInterface $redisClient, AwsSqsUtil $awsSqsUtil, AwsS3Util $awsS3Util, EmailService $emailService, TranslatorInterface $translator)
    {
        $this->repository = $entityManager->getRepository(TranscodingQueue::class);
        $this->entityManager = $entityManager;
        $this->redisClient = $redisClient;
        $this->awsSqsUtil = $awsSqsUtil;
        $this->awsS3Util = $awsS3Util;
        $this->emailService = $emailService;
        $this->translator = $translator;
    }

    /**
     * @param $id
     * @return null|TranscodingQueue|Object
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param $id
     * @return null|TranscodingQueue|Object
     */
    public function findByAwsJobId($id)
    {
        return $this->repository->findOneBy(["awsJobId" => $id]);
    }


    /**
     * @param Vod $vod
     * @param $awsJobDetails
     * @return TranscodingQueue
     */
    public function init(Vod $vod)
    {
        $transcodingQueue = new TranscodingQueue();
        $transcodingQueue->vod = $vod;

        return $this->save($transcodingQueue);
    }

    /**
     * @param TranscodingQueue $transcodingQueue
     * @param $awsJobDetails
     * @return TranscodingQueue
     */
    public function saveJobInfo(TranscodingQueue $transcodingQueue, $awsJobDetails)
    {
        $transcodingQueue->awsJobId = $awsJobDetails["Job"]["Id"];
        $transcodingQueue->jobDetails = $awsJobDetails;
        $transcodingQueue->status = $awsJobDetails["Job"]["Status"];
        return $this->save($transcodingQueue);
    }

    /**
     * @param TranscodingQueue $transcodingQueue
     * @return TranscodingQueue
     */
    public function save(TranscodingQueue $transcodingQueue)
    {
        $this->entityManager->persist($transcodingQueue);
        $this->entityManager->flush();
        return $transcodingQueue;
    }

    /**
     * @return mixed
     */
    public function transcodingInQueue()
    {
        return $this->repository->findTotalProcessInQueue([
            TranscodingQueueStatusEnum::COMPLETE,
            TranscodingQueueStatusEnum::ERROR,
            TranscodingQueueStatusEnum::INITIALIZED
            ]);
    }

    /**
     * @return mixed
     */
    public function transcodingFinishedPerDay()
    {
        return $this->repository->findTotalPerDay(["COMPLETE"]);
    }

    public function limitExideEmail()
    {
        return $this->emailService->sendEmail("emails/transcoding_limit_exceeded_error.html.twig",
        getenv("ADMIN_ALERT_EMAIL"),
            [
            ], $this->translator->trans('email.transcoding_limit_exceeded_error.subject'));
    }


}
