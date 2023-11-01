<?php

namespace App\Command;

use App\Entity\EndUser;
use App\Entity\LineUp;
use App\Entity\Show;
use App\Entity\Source;
use App\Entity\SourceVideo;
use App\Entity\Story;
use App\Util\AwsSqsUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class IvnewsVneIntegrationCommand extends Command
{
    protected static $defaultName = 'IvnewsVneIntegrationCommand';
    protected static $defaultDescription = 'Add a short description for your command';

    /**
     * @var AwsSqsUtil
     */
    private  $awsSqsUtil;
    private $entityManager;


    public function __construct(AwsSqsUtil $awsSqsUtil, EntityManagerInterface $entityManager )
    {
        parent::__construct();
        $this->awsSqsUtil = $awsSqsUtil;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->pushSource($io);
        $this->pushSourceVideo($io);
        $this->pushShow($io);
        $this->pushStory($io);
        $this->pushEndUser($io);
        $this->pushLineup($io);

        return 0;
    }

    /**
     * @param SymfonyStyle $io
     * @return int
     */
    public function pushSource(SymfonyStyle $io)
    {

        $sources= $this->entityManager->getRepository(Source::class)->findAll();

        /** @var Source $source */
        foreach ($sources as $source) {

            $queueUrl = $this->awsSqsUtil->getQueueUrl($_ENV["VNE_INTEGRATION_SQS_QUEUE_NAME"]);

            $this->awsSqsUtil->sendMessage($queueUrl ,
                json_encode([
                    "id"=>$source->getId(),
                    "type" => 'Source',
                    "method" => 'POST'
                ])
            );

        }
        return 0;
    }

    /**
     * @param SymfonyStyle $io
     * @return int
     */
    public function pushSourceVideo(SymfonyStyle $io)
    {

        $sourceVideos= $this->entityManager->getRepository(SourceVideo::class)->findAll();

        /** @var SourceVideo $sourceVideo */
        foreach ($sourceVideos as $sourceVideo) {

            $queueUrl = $this->awsSqsUtil->getQueueUrl($_ENV["VNE_INTEGRATION_SQS_QUEUE_NAME"]);

            $this->awsSqsUtil->sendMessage($queueUrl ,
                json_encode([
                    "id"=>$sourceVideo->getId(),
                    "type" => 'SourceVideo',
                    "method" => 'POST'
                ])
            );
        }
        return 0;
    }

    /**
     * @param SymfonyStyle $io
     * @return int
     */
    public function pushShow(SymfonyStyle $io)
    {

        $shows= $this->entityManager->getRepository(Show::class)->findAll();

        /** @var Show $show */
        foreach ($shows as $show) {

            $queueUrl = $this->awsSqsUtil->getQueueUrl($_ENV["VNE_INTEGRATION_SQS_QUEUE_NAME"]);

            $this->awsSqsUtil->sendMessage($queueUrl ,
                json_encode([
                    "id"=>$show->getId(),
                    "type" => 'Show',
                    "method" => 'POST'
                ])
            );
        }
        return 0;
    }

    /**
     * @param SymfonyStyle $io
     * @return int
     */
    public function pushStory(SymfonyStyle $io)
    {

        $stories= $this->entityManager->getRepository(Story::class)->findAll();

        /** @var Story $story */
        foreach ($stories as $story) {

            $queueUrl = $this->awsSqsUtil->getQueueUrl($_ENV["VNE_INTEGRATION_SQS_QUEUE_NAME"]);

            $this->awsSqsUtil->sendMessage($queueUrl ,
                json_encode([
                    "id"=>$story->getId(),
                    "type" => 'Story',
                    "method" => 'POST'
                ])
            );
        }

        return 0;
    }

    /**
     * @param SymfonyStyle $io
     * @return int
     */
    public function pushEndUser(SymfonyStyle $io)
    {

        $endUsers= $this->entityManager->getRepository(EndUser::class)->findAll();

        /** @var EndUser $endUser */
        foreach ($endUsers as $endUser) {

            $queueUrl = $this->awsSqsUtil->getQueueUrl($_ENV["VNE_INTEGRATION_SQS_QUEUE_NAME"]);

            $this->awsSqsUtil->sendMessage($queueUrl ,
                json_encode([
                    "id"=>$endUser->getId(),
                    "type" => 'EndUser',
                    "method" => 'POST'
                ])
            );
        }
        return 0;
    }

    /**
     * @param SymfonyStyle $io
     * @return int
     */
    public function pushLineup(SymfonyStyle $io)
    {

        $lineUps= $this->entityManager->getRepository(LineUp::class)->findAll();

        /** @var LineUp $lineUp */
        foreach ($lineUps as $lineUp) {

            $queueUrl = $this->awsSqsUtil->getQueueUrl($_ENV["VNE_INTEGRATION_SQS_QUEUE_NAME"]);

            $this->awsSqsUtil->sendMessage($queueUrl ,
                json_encode([
                    "id"=>$lineUp->getId(),
                    "type" => 'LineUp',
                    "method" => 'POST'
                ])
            );
        }
        return 0;
    }
}
