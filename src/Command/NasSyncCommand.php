<?php

namespace App\Command;

use ApiPlatform\Core\GraphQl\Type\Definition\UploadType;
use App\Entity\Folders;
use App\Entity\SourceVideo;
use App\Entity\Vod;
use App\Enums\SourceUploadTypeEnum;
use App\Enums\SourceVideoStatusEnum;
use App\Enums\VodStatusEnum;
use App\Services\SourceVideoService;
use App\Services\VodService;
use App\Util\AwsS3Util;
use App\Util\AwsSqsUtil;
use Aws\S3\S3Client;
use Chrisyue\PhpM3u8\Facade\ParserFacade;
use Chrisyue\PhpM3u8\Stream\TextStream;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class NasSyncCommand extends Command
{
    protected static $defaultName = 'ivnews:nas-sync';
    protected static $defaultDescription = 'Add a short description for your command';

    /** @var $awsS3Util  AwsS3Util */
    protected $awsS3Util;
    
    /** @var  EntityManagerInterface */
    protected $entityManager;

    
    /** @var  VodService */
    protected $vodService;

    
    /** @var  SourceVideoService */
    protected $sourceVideoService;

    /**
     * @var AwsSqsUtil
     */
    private  $awsSqsUtil;

    public function __construct(AwsS3Util $awsS3Util,AwsSqsUtil $awsSqsUtil, EntityManagerInterface $entityManager, VodService $vodService, SourceVideoService $sourceVideoService)
    {
        parent::__construct();
        $this->awsS3Util = $awsS3Util;
        $this->awsSqsUtil = $awsSqsUtil;
        $this->entityManager = $entityManager;
        $this->vodService = $vodService;
        $this->sourceVideoService = $sourceVideoService;
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
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }
        
        $RAW_QUERY = 'SELECT sub_folder, id  FROM folders where deleted_at is NULL and sub_folder is not null order by id desc ;';
        
        $statement = $this->entityManager->getConnection()->prepare($RAW_QUERY);
        $statement->execute();
        $resultSet = $statement->fetchAll();


        foreach($resultSet as $result ){
            $this->awsS3Util->setClient($_ENV["AWS_S3_NAS_ACCESS_KEY"],$_ENV["AWS_S3_NAS_SECRET_KEY"]);
            $s3Client = $this->awsS3Util->getClient();
            $prefix = $result["sub_folder"];
            $folderId = $result["id"];
            
            $objects = $s3Client->getPaginator("ListObjects",
            [
                'Bucket' => $_ENV["NAS_SYNC_BUCKET"],
                'Prefix' => $prefix
            ]);
        
            foreach ($objects as $listResponse) {
                $items = $listResponse->search("Contents[?contains(Key, 'vtt')]");
               
                foreach($items as $item) {
                    
                        $key = $item["Key"];
                        $output->writeln("==================");
                        $output->writeln($key);
                        $output->writeln($folderId);
                        $output->writeln("==================");
                        
                        $videoDirectoryArray = $slugArray = $keyArray = explode("/", $key);
                        array_splice($slugArray, -1);

                        $sourceVideoSlug = implode("_", $slugArray);
                        array_pop($videoDirectoryArray);

                        $videoDirectory = implode("/", $videoDirectoryArray);
                        
                        $vttKey = "$videoDirectory/output.vtt";
                        $mp3Key = "$videoDirectory/output.mp3";
                        $hlsKey = "$videoDirectory/hls/index.m3u8";
                        $originalFileCreationTimeKey = "$videoDirectory/original file creation time.txt";
                        
                        $sourceVideoTemp = new SourceVideo();
                        $sourceVideoSlug = $sourceVideoTemp->slugify($sourceVideoSlug);
                        /** @var Folders $folder */
                        $folder = $this->entityManager->getRepository(Folders::class)->find($folderId);
                        if(!empty($folder)) {
                            
                            $sourceVideoExist = $this->entityManager->getRepository(SourceVideo::class)->findOneBy(["slug" => $sourceVideoSlug]);

                            if(empty($sourceVideoExist)) {
                                $hlsArray = explode("/", $hlsKey);
                                $fileName = $hlsArray[count($hlsArray) - 3];
                                $fileNamePathInfo = pathinfo($fileName);

                                $vod = $this->vodService->initVod($fileNamePathInfo["basename"],$fileName,$fileNamePathInfo["extension"], $hlsKey, $hlsKey  , $_ENV["NAS_SYNC_BUCKET"]);
                            
                                $cmd = $s3Client->getCommand('GetObject', [
                                    'Bucket' => $_ENV["NAS_SYNC_BUCKET"],
                                    'Key' => $hlsKey
                                ]);
                                
                                $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');
                                // Get the actual presigned-url
                                $presignedUrl = (string)$request->getUri();
                                $parser = new ParserFacade();
                                $content = file_get_contents($presignedUrl);
                                /**
                                 * @var ArrayObject
                                 */
                                $mediaPlaylist = $parser->parse(new TextStream($content));
                                $mediaSegments = $mediaPlaylist["mediaSegments"];
                                $duration = 0;
                                foreach($mediaSegments as $tsSegment){
                                    
                                    $duration += $tsSegment["EXTINF"]->getDuration();

                                }
                                $vod->duration = $duration;
                                $vod->status = VodStatusEnum::READY;
                                $vod = $this->vodService->save($vod);

                                // Get Created Time
                                $cmd = $s3Client->getCommand('GetObject', [
                                    'Bucket' => $_ENV["NAS_SYNC_BUCKET"],
                                    'Key' => $originalFileCreationTimeKey
                                ]);

                                $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');
                                // Get the actual presigned-url
                                $presignedUrl = (string)$request->getUri();
                                $originalDateTime = file_get_contents($presignedUrl);

                                if(!empty($originalDateTime)) {
                                    $originalDateArray = explode(" ", $originalDateTime);
                                    $originalDate = $originalDateArray[0];
                                    $originalTime = explode(".",$originalDateArray[1]);
                                    $originalTime = $originalTime[0];
                                    $timeZone = $folder->timeZone->phpTimeZone;
                                    
                                    if (date('I')) {
                                        $timeZone = $folder->timeZone->phpTimeZoneDLS;
                                    }
                                    unset($originalCreationTime);

                                    $output->writeln("========================================");
                                    $output->writeln("{$originalDate} {$originalTime} {$originalDateArray[2]}");
                                    $output->writeln("========================================");
                                    $originalCreationTime = new \DateTime("{$originalDate} {$originalTime} {$originalDateArray[2]}");
                                    // var_dump($originalCreationTime);
                                    // $originalCreationTime = $originalCreationTime->createFromFormat('Y-m-d H:i:s O', "{$originalDate} {$originalTime} {$originalDateArray[2]}");
                                    $originalCreationTime->setTimezone(new \DateTimeZone("UTC"));
                                    var_dump($originalCreationTime);

                                    $testTime1 = new DateTime("{$originalDate} {$folder->getPublicationDate()}", new \DateTimeZone($timeZone)); 
                                    $testTime1->setTimezone(new \DateTimeZone("UTC")); 
                                    $testTime1->setDate($originalCreationTime->format('Y'),$originalCreationTime->format('m'), $originalCreationTime->format('d'));
                                    var_dump($testTime1);

                                    $testTime2 = new DateTime("{$originalDate} {$folder->getPublicationDate()}", new \DateTimeZone($timeZone)); 
                                    $testTime2->setTimezone(new \DateTimeZone("UTC")); 
                                    $testTime2->setDate($originalCreationTime->format('Y'),$originalCreationTime->format('m'), $originalCreationTime->format('d'));
                                    $testTime2->modify('-1day');
                                    var_dump($testTime2);
                                   
                                }
                                
                                // Calling the diff() function on above
                                // two DateTime objects
                                $difference = $originalCreationTime->diff($testTime1);
                               
                                $testTime1Diff = $difference->days * 24 * 60;
                                $testTime1Diff += $difference->h * 60;
                                $testTime1Diff += $difference->i;
                                echo $testTime1Diff.' minutes';

                                $difference = $originalCreationTime->diff($testTime2);
                                $testTime2Diff = $difference->days * 24 * 60;
                                $testTime2Diff += $difference->h * 60;
                                $testTime2Diff += $difference->i;
                                echo $testTime2Diff.' minutes';
                                
                                $output->writeln("======== original Creation Time==========");
                                
                                $sourceVideo = new SourceVideo();
                                $sourceVideo->title = $fileNamePathInfo["basename"];
                                $sourceVideo->vod = $vod;
                                $sourceVideo->status = SourceVideoStatusEnum::READY_FOR_MARKER;
                                $sourceVideo->readyFotMarkupAt = new \DateTime();
                                $sourceVideo->uploadedType = SourceUploadTypeEnum::NAS;
                                    
                                if($testTime1Diff < $testTime2Diff) {
                                    $output->writeln("======== timezone $timeZone  ==1==========");
                                    $sourceVideo->publicationDate =  $testTime1;
                                } else {
                                    $sourceVideo->publicationDate =  $testTime2;
                                }
                                
                                $sourceVideo->show = $folder->show;
                                $sourceVideo->setSlug($sourceVideoSlug);
                                $sourceVideo->timeZone = $folder->timeZone;
                                $this->sourceVideoService->save($sourceVideo);
                                $vod->sourceVideo = $sourceVideo;
                                $vod = $this->vodService->save($vod);

                                $queueUrl = $this->awsSqsUtil->getQueueUrlFromFullName(getenv("VNE_AUDIO_LINK_SQS_QUEUE_NAME"));
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

                                $output->writeln("================== SAVED ");
                                $output->writeln($sourceVideo->getId());
                                $output->writeln("==================");
                            }

                            $dataRetrievalAt = new \DateTime();
                            $dataRetrievalAt->setTimezone(new \DateTimeZone("UTC"));
                            $folder->dataRetrievalAt = $dataRetrievalAt;
                            $folder->dataRetrievalStatus = "success";
                            $this->entityManager->persist($folder);
                            $this->entityManager->flush();
                            $output->writeln("================== Folder Updated successfully ===================");

                        } else {
                                $output->writeln("================== already exist ===================");
                                $output->writeln($sourceVideoSlug);
                                $output->writeln("==================***************===================");
                        }
                }
            }
    } 

    return 0;
    }
}
