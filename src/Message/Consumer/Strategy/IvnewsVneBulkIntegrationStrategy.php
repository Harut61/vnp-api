<?php

namespace App\Message\Consumer\Strategy;


use App\Entity\EndUser;
use App\Entity\LineUp;
use App\Entity\Show;
use App\Entity\Source;
use App\Entity\SourceVideo;
use App\Entity\Story;
use App\Model\Message;
use App\Repository\EndUserRepository;
use App\Repository\LineupRepository;
use App\Repository\ShowRepository;
use App\Repository\SourceRepository;
use App\Repository\SourceVideoRepository;
use App\Repository\StoryRepository;
use App\Util\AwsSqsUtil;
use App\Util\AwsSqsUtilInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class IvnewsVneBulkIntegrationStrategy implements StrategyInterface
{
    public const QUEUE_NAME = "vnp-vne-bulk-sync";
    /**
     * @var AwsSqsUtil
     */
    private $awsSqsUtil;

    private $logger;

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var  SourceRepository */
    private $sourceRepository;

    /** @var  SourceVideoRepository */
    private $sourceVideoRepository;

    /** @var  ShowRepository */
    private $showRepository;

    /** @var  StoryRepository */
    private $storyRepository;

    /** @var  EndUserRepository */
    private $endUserRepository;

    /** @var  LineupRepository */
    private $lineupRepository;

    /**
     * VneIntegrationStrategy constructor.
     * @param AwsSqsUtilInterface $awsSqsUtil
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        AwsSqsUtilInterface $awsSqsUtil,
        LoggerInterface $logger,
        EntityManagerInterface $entityManager,
        SourceRepository $sourceRepository,
        SourceVideoRepository $sourceVideoRepository,
        ShowRepository $showRepository,
        StoryRepository $storyRepository,
        EndUserRepository $endUserRepository,
        LineupRepository $lineupRepository
    )
    {
        $this->sourceRepository = $entityManager->getRepository(Source::class);
        $this->sourceVideoRepository = $entityManager->getRepository(SourceVideo::class);
        $this->showRepository = $entityManager->getRepository(Show::class);
        $this->storyRepository = $entityManager->getRepository(Story::class);
        $this->endUserRepository = $entityManager->getRepository(EndUser::class);
        $this->lineupRepository = $entityManager->getRepository(LineUp::class);
        $this->awsSqsUtil = $awsSqsUtil;
        $this->logger = $logger;
        $this->entityManager = $entityManager;

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

            $startDate = \DateTime::createFromFormat('Y-m-d', $body['startDate']);
            $endDate = \DateTime::createFromFormat('Y-m-d', $body['endDate']);


            if ($body["contentType"] == "Source") {

                $this->echoMsg("Content Type  Source Found");
                var_dump($body);
                /** @var SourceRepository $sources */
                $sources = $this->sourceRepository->findBetween($startDate, $endDate);

                /** @var Source $source */
                foreach ($sources as $source) {
                    try {
                        $queueUrl = $this->awsSqsUtil->getQueueUrl($_ENV["VNE_INTEGRATION_SQS_QUEUE_NAME"]);
                        $this->echoMsg("processing source {$source->getId()}");

                        $this->awsSqsUtil->sendMessage($queueUrl,
                            json_encode([
                                "id" => $source->getId(),
                                "type" => $body['contentType'],
                                "startDate" => $body['startDate'],
                                "endDate" => $body['endDate'],
                                "method" => "POST"

                            ])
                        );
                        $this->echoMsg("Msg Sent for {$source->getId()}");
                    } catch (\Exception $exception) {
                        echo "==== Exception " . $exception->getMessage() . "======\n";
                        $this->awsSqsUtil->deleteMessage($message);
                    }
                }


            }

            if ($body["contentType"] == "SourceVideo") {
                $this->echoMsg("Content Type  SourceVideo Found");
                var_dump($body);
                /** @var SourceVideoRepository $sourceVideos */
                $sourceVideos = $this->sourceVideoRepository->findBetween($startDate, $endDate);

                /** @var SourceVideo $sourceVideo */
                foreach ($sourceVideos as $sourceVideo) {
                    try {
                        $this->echoMsg("processing sourceVideo {$sourceVideo->getId()}");
                        $this->echoMsg("vne audio sourceVideo queue VNE_AUDIO_LINK_SQS_QUEUE_NAME is +++ {$_ENV["VNE_AUDIO_LINK_SQS_QUEUE_NAME"]} +++");

                        $queueUrl = $this->awsSqsUtil->getQueueUrlFromFullName($_ENV["VNE_AUDIO_LINK_SQS_QUEUE_NAME"]);
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

                        $this->echoMsg("Msg Sent for {$sourceVideo->getId()}");
                    } catch (\Exception $exception) {
                        echo "==== Exception " . $exception->getMessage() . "======\n";
                        $this->awsSqsUtil->deleteMessage($message);
                    }
                }
            }


            if ($body["contentType"] == "Show") {
                $this->echoMsg("Content Type  Show Found");
                var_dump($body);
                /** @var ShowRepository $shows */
                $shows = $this->showRepository->findBetween($startDate, $endDate);

                /** @var Show $show */
                foreach ($shows as $show) {
                    try {
                        $queueUrl = $this->awsSqsUtil->getQueueUrl($_ENV["VNE_INTEGRATION_SQS_QUEUE_NAME"]);
                        $this->echoMsg("processing show {$show->getId()}");

                        $this->awsSqsUtil->sendMessage($queueUrl,
                            json_encode([
                                "id" => $show->getId(),
                                "type" => $body['contentType'],
                                "startDate" => $body['startDate'],
                                "endDate" => $body['endDate'],
                                "method" => "POST"
                            ])
                        );
                        $this->echoMsg("Msg Sent for {$show->getId()}");
                    } catch (\Exception $exception) {
                        echo "==== Exception " . $exception->getMessage() . "======\n";
                        $this->awsSqsUtil->deleteMessage($message);
                    }
                }
            }

            if ($body["contentType"] == "Story") {
                $this->echoMsg("Content Type  Story Found");
                var_dump($body);
                /** @var StoryRepository $stories */
                $stories = $this->storyRepository->findBetween($startDate, $endDate);

                /** @var Story $story */
                foreach ($stories as $story) {
                    try {
                        $queueUrl = $this->awsSqsUtil->getQueueUrl($_ENV["VNE_INTEGRATION_SQS_QUEUE_NAME"]);
                        $this->echoMsg("processing story {$story->getId()}");

                        $this->awsSqsUtil->sendMessage($queueUrl,
                            json_encode([
                                "id" => $story->getId(),
                                "type" => $body['contentType'],
                                "startDate" => $body['startDate'],
                                "endDate" => $body['endDate'],
                                "method" => "POST"
                            ])
                        );
                        $this->echoMsg("Msg Sent for {$story->getId()}");
                    } catch (\Exception $exception) {
                        echo "==== Exception " . $exception->getMessage() . "======\n";
                        $this->awsSqsUtil->deleteMessage($message);
                    }
                }
            }

            if ($body["contentType"] == "EndUser") {
                $this->echoMsg("Content Type  EndUser Found");
                var_dump($body);
                /** @var EndUserRepository $endUsers */
                $endUsers = $this->endUserRepository->findBetween($startDate, $endDate);

                /** @var EndUser $endUser */
                foreach ($endUsers as $endUser) {
                    try {
                        $queueUrl = $this->awsSqsUtil->getQueueUrl($_ENV["VNE_INTEGRATION_SQS_QUEUE_NAME"]);
                        $this->echoMsg("processing endUser {$endUser->getId()}");

                        $this->awsSqsUtil->sendMessage($queueUrl,
                            json_encode([
                                "id" => $endUser->getId(),
                                "type" => $body['contentType'],
                                "startDate" => $body['startDate'],
                                "endDate" => $body['endDate'],
                                "method" => "POST"
                            ])
                        );
                        $this->echoMsg("Msg Sent for {$endUser->getId()}");
                    } catch (\Exception $exception) {
                        echo "==== Exception " . $exception->getMessage() . "======\n";
                        $this->awsSqsUtil->deleteMessage($message);
                    }
                }
            }

            if ($body["contentType"] == "LineUp") {
                $this->echoMsg("Content Type  LineUp Found");
                var_dump($body);
                /** @var LineupRepository $lineUps */
                $lineUps = $this->lineupRepository->findBetween($startDate, $endDate);

                /** @var LineUp $lineUp */
                foreach ($lineUps as $lineUp) {
                    try {
                        $queueUrl = $this->awsSqsUtil->getQueueUrl($_ENV["VNE_INTEGRATION_SQS_QUEUE_NAME"]);
                        $this->echoMsg("processing lineUp {$lineUp->getId()}");

                        $this->awsSqsUtil->sendMessage($queueUrl,
                            json_encode([
                                "id" => $lineUp->getId(),
                                "type" => $body['contentType'],
                                "startDate" => $body['startDate'],
                                "endDate" => $body['endDate'],
                                "method" => "POST"
                            ])
                        );
                        $this->echoMsg("Msg Sent for {$lineUp->getId()}");
                    } catch (\Exception $exception) {
                        echo "==== Exception " . $exception->getMessage() . "======\n";
                        $this->awsSqsUtil->deleteMessage($message);
                    }
                }
            }
            $this->awsSqsUtil->deleteMessage($message);


        } catch (\Exception $exception) {

            // Todo send message to sentry
            echo "==== Exception " . $exception->getMessage() . "======\n";
            $this->awsSqsUtil->deleteMessage($message);
            $this->logger->alert(sprintf('The message "%s" has been put in the "flight" mode.', $message->id));
        }

    }

    private function echoMsg($msg)
    {
        echo "========== $msg =========\n";
    }

}