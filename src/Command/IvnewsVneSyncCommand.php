<?php

namespace App\Command;

use App\Entity\AdminRoles;
use App\Entity\AdminUser;
use App\Entity\HighLevelSubjectTag;
use App\Entity\Segment;
use App\Entity\StoryType;
use App\Entity\TranscodingProfile;
use App\Entity\TranscodingProfileOption;
use App\Enums\UserRoleEnum;
use App\Repository\AdminUserRepository;
use App\Services\PaasSetupService;
use App\Services\Vne\HighLevelSubjectService;
use App\Services\Vne\SegmentService;
use App\Services\Vne\StoryTypeService;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class IvnewsVneSyncCommand extends Command
{
    protected static $defaultName = 'ivnews:vne-sync';

    private $container;
    private $entityManager;
    private $encoder;
    private $storyTypeService;
    private $highLevelSubjectService;
    private $segmentService;

    public function __construct(ContainerInterface$container, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder, StoryTypeService $storyTypeService , HighLevelSubjectService $highLevelSubjectService, SegmentService $segmentService )
    {
        parent::__construct();
        $this->container = $container;
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
        $this->storyTypeService = $storyTypeService;
        $this->highLevelSubjectService = $highLevelSubjectService;
        $this->segmentService = $segmentService;
    }

    protected function configure()
    {
        $this
            ->setDescription('Import Default Config Before Setup Environment');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->writeln('=============================== CREATING STORY TYPE ===========================');
        $this->createStoryTypes($io);
        $io->writeln('=============================== CREATING HIGH LEVEL SUBJECTS ===========================');
        $this->createHighLevelSubject($io);
        $io->writeln('=============================== CREATING SEGMENTS ===========================');
        $this->createSegment($io);


        return 0;
    }

    /**
     * @param SymfonyStyle $io
     * @param $result
     * @param $importedMsg
     */
    private function printOutput(SymfonyStyle $io , $result, $importedMsg)
    {
        $io->writeln('');
        $io->writeln('');
        $io->writeln('########');
        $io->writeln('   ##');
        $io->writeln('########');

        foreach ($result as $re)
        {
            $io->writeln($re);
        }
        $io->success($importedMsg);
    }


    /**
     * @param SymfonyStyle $io
     * @return int
     */
    public function createStoryTypes(SymfonyStyle $io)
    {

        $vneStoryTypes = $this->storyTypeService->index("",1,1000,[]);

        foreach ($vneStoryTypes as $vneStoryType) {

            $storyTypeRepo = $this->entityManager->getRepository(StoryType::class);
            /** @var StoryType $storyType */
            $storyType = $storyTypeRepo->findOneBy(["vneId" => $vneStoryType["id"]]);

            if ($storyType) {
                $io->writeln("######## Story Type already exist {$vneStoryType["id"]}  #########");
                continue;
            } else {
                // add StoryType
                $storyType = new StoryType();
                $storyType->vneTitle = $storyType->vneTitle = $vneStoryType["name"];
                $storyType->vneId = $vneStoryType["id"];
                $this->entityManager->persist($storyType);
                $io->writeln("######## Story Type Added {$vneStoryType["id"]}  #########");
            }
        }

        $this->entityManager->flush();
        $io->success('VNE story Types Subject Sync SuccessFully!');
    }


    /**
     * @param SymfonyStyle $io
     * @return int
     */
    public function createHighLevelSubject(SymfonyStyle $io)
    {

        $vneHighLevelSubjects = $this->highLevelSubjectService->index("",1,1000,[]);

        foreach ($vneHighLevelSubjects as $vneHighLevelSubject) {

            $highLevelSubjectRepo = $this->entityManager->getRepository(HighLevelSubjectTag::class);
            /** @var HighLevelSubjectTag $highLevelSubject */
            $highLevelSubject = $highLevelSubjectRepo->findOneBy(["vneId" => $vneHighLevelSubject["id"]]);

            if ($highLevelSubject) {
                $io->writeln("######## High Level Subject already exist {$vneHighLevelSubject["id"]}  #########");
                continue;
            } else {
                // add High Level Subjects
                $highLevelSubject = new HighLevelSubjectTag();
                $highLevelSubject->vneTitle = $highLevelSubject->vneTitle = $vneHighLevelSubject["name"];
                $highLevelSubject->vneId = $vneHighLevelSubject["id"];
                $this->entityManager->persist($highLevelSubject);
                $io->writeln("######## High Level Subject Added {$vneHighLevelSubject["id"]}  #########");
            }
        }

        $this->entityManager->flush();
        $io->success('VNE High Level Subject Sync SuccessFully!');
    }

    /**
     * @param SymfonyStyle $io
     * @return int
     */
    public function createSegment(SymfonyStyle $io)
    {

        $vneSegments = $this->segmentService->index("",1,1000,[]);

        foreach ($vneSegments as $vneSegment) {

            $segmentRepo = $this->entityManager->getRepository(Segment::class);
            /** @var Segment $segment */
            $segment = $segmentRepo->findOneBy(["vneId" => $vneSegment["id"]]);

            if ($segment) {
                $io->writeln("######## Segment already exist {$vneSegment["id"]}  #########");
                continue;
            } else {
                // add segments
                $segment = new Segment();
                $segment->vneTitle = $segment->vneTitle = $vneSegment["name"];
                $segment->vneId = $vneSegment["id"];
                $segment->title = $vneSegment["name"];
                $this->entityManager->persist($segment);
                $io->writeln("######## Segment Added {$vneSegment["id"]}  #########");
            }
        }

        $this->entityManager->flush();
        $io->success('VNE Segment Sync SuccessFully!');
    }

}
