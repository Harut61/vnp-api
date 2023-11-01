<?php

namespace App\Command;

use App\Entity\Interstitial;
use App\Entity\Segment;
use App\Enums\InterstitialTimeOfDayEnum;
use App\Enums\SegmentTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateInterstitialCommand extends Command
{
    protected static $defaultName = 'ivnews:create:interstitial';
    protected static $defaultDescription = 'create interstitial when new segment added';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

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
        $this->createInterstitial($io);
        return 0;
    }


    /**
     * @param SymfonyStyle $io
     * @return int
     */
    public function createInterstitial(SymfonyStyle $io)
    {

        $segmentTypes = SegmentTypeEnum::getConstants();
        foreach ($segmentTypes as $segmentType){
            if($segmentType == "INTRO" || $segmentType == "OUTRO"){
                $timeDays = InterstitialTimeOfDayEnum::getConstants();
                foreach ($timeDays as $timeDay){
                    $slugInterstitial = $segmentType.'-'.$timeDay;
                    $intSlug = $this->entityManager->getRepository(Interstitial::class)->findOneBy(['slug' => $this->slugify($slugInterstitial)]);
                    if(empty($intSlug)){
                        $interstitial = new Interstitial();
                        $interstitial->title = $segmentType.'-'.$timeDay;
                        $interstitial->timeOfDay= $timeDay;
                        $interstitial->setSlug($slugInterstitial);
                        $this->entityManager->persist($interstitial);
                        $this->entityManager->flush();
                    }

                }
            }
            elseif ($segmentType == "STORY_TRANSITION" || $segmentType == "TROUBLE_LOOP" || $segmentType == "SUBSCRIBE_NOW"){
                $interSlug = $this->entityManager->getRepository(Interstitial::class)->findOneBy(['slug' => $this->slugify($segmentType)]);
                if(empty($interSlug)){
                    $interstitial = new Interstitial();
                    $interstitial->title = $segmentType;
                    $interstitial->setSlug($segmentType);
                    $this->entityManager->persist($interstitial);
                    $this->entityManager->flush();
                }
            }

        }

        $segments = $this->entityManager->getRepository(Segment::class)->findAll();
        /** @var Segment $segment */
        foreach ($segments as $segment)
        {
            if($segment->type == "INTRO" || $segment->type == "SEGMENT_INTRO" || $segment->type == "OUTRO"){
                $timeOfDays = InterstitialTimeOfDayEnum::getConstants();
                foreach ($timeOfDays as $timeOfDay){
                    $slug = $segment->title.'-'.$segment->type.'-'.$timeOfDay;
                    $interstitialSlug = $this->entityManager->getRepository(Interstitial::class)->findOneBy(['slug' => $this->slugify($slug)]);
                    if(empty($interstitialSlug)){
                        $interstitial = new Interstitial();
                        $interstitial->title = $slug;
                        $interstitial->setSlug($slug);
                        $interstitial->timeOfDay = $timeOfDay;
                        $interstitial->segment = $segment;
                        $this->entityManager->persist($interstitial);
                        $this->entityManager->flush();
                    }
                }
            }
            else{
                $slugOne = $segment->title.'-'.$segment->type;
                $interstitialSlugOne = $this->entityManager->getRepository(Interstitial::class)->findOneBy(['slug' => $this->slugify($slugOne)]);
                if(empty($interstitialSlugOne)){
                    $interstitial = new Interstitial();
                    $interstitial->title = $slugOne;
                    $interstitial->setSlug($slugOne);
                    $interstitial->segment = $segment;
                    $this->entityManager->persist($interstitial);
                    $this->entityManager->flush();
                }
            }
        }

        $this->entityManager->flush();
        $io->success('Interstitial Create SuccessFully!');
    }

    public function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        //   $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}
