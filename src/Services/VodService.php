<?php
namespace App\Services;

use App\Entity\SourceVideo;
use App\Entity\Vod;
use App\Enums\SourceUploadTypeEnum;
use App\Enums\VodStatusEnum;
use App\Services\Aws\SqsService;
use App\Util\AwsSqsUtil;
use App\Util\AwsS3Util;
use App\Util\B2Util;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Mhor\MediaInfo\Type\General;
use Mhor\MediaInfo\Type\Video;
use Predis\ClientInterface;
use Snc\RedisBundle\Client\Phpredis\Client;
use Mhor\MediaInfo\MediaInfo;

/**
 * Class VodService
 * @package App\Services
 */
class VodService
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

    /** @var  $b2Util B2Util */
    private $b2Util;

    /** @var  $emailService EmailService */
    private $emailService;



    public function __construct(EntityManagerInterface $entityManager, ClientInterface $redisClient, AwsSqsUtil $awsSqsUtil, AwsS3Util $awsS3Util, EmailService $emailService ,  B2Util $b2Util)
    {
        $this->repository = $entityManager->getRepository(Vod::class);
        $this->entityManager = $entityManager;
        $this->redisClient = $redisClient;
        $this->awsSqsUtil = $awsSqsUtil;
        $this->awsS3Util = $awsS3Util;
        $this->b2Util = $b2Util;
        $this->emailService = $emailService;
    }

    /**
     * @param $id
     * @return null|Vod|Object
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param $playbackId
     * @return null|object
     */
    public function findByPlaybackId($playbackId)
    {
        return  $this->repository->findOneBy(["playBackId" => $playbackId]);
    }

    /**
     * @param $title
     * @param $originalFileName
     * @param $originalExtension
     * @param $originalFileUrl
     * @param $originalFilePath
     * @param $originalFileBucket
     * @return Vod
     */
    public function initVod($title, $originalFileName, $originalExtension, $originalFileUrl, $originalFilePath, $originalFileBucket)
    {
        $vod = new Vod();
        $vod->status = VodStatusEnum::INITIALIZED;
        $vod->title = $title;
        $vod->originalFileName = $originalFileName;
        $vod->originalExtension = $originalExtension;
        $vod->originalFileUrl = $originalFileUrl;
        $vod->originalFilePath = $originalFilePath;
        $vod->originalFileBucket = $originalFileBucket;

        return $this->save($vod);
    }


    /**
     * @param Vod $vod
     * @param $mediaInfo
     * @return Vod
     */
    public function create(Vod $vod, $mediaInfo)
    {

        $vod->status = VodStatusEnum::TRANSCODING_IN_A_QUEUE;
        $vod->mediaInfo = $mediaInfo;

        $vod->videoBitrate = $mediaInfo["videos"]["bit_rate"]["absoluteValue"];
        $vod->videoBitrateTxt = $mediaInfo["videos"]["bit_rate"]["textValue"];

        $vod->videoFps = $mediaInfo["videos"]["frame_rate"]["absoluteValue"];
        $vod->videoFpsTxt = $mediaInfo["videos"]["frame_rate"]["textValue"];

        $vod->videoWidth = $mediaInfo["videos"]["width"]["absoluteValue"];
        $vod->videoWidthTxt = $mediaInfo["videos"]["width"]["textValue"];

        $vod->videoHeight = $mediaInfo["videos"]["height"]["absoluteValue"];
        $vod->videoHeightTxt = $mediaInfo["videos"]["height"]["textValue"];
        $vod->videoCodec = $mediaInfo["videos"]["format"]["shortName"];

        $vod->duration = $mediaInfo["general"]["duration"]["milliseconds"] / 1000;
        $vod->mediaType = $this->getValueIfArrayKeyExist("internet_media_type", $mediaInfo["general"]);

        $vod->audioLanguage = $this->getValueIfArrayKeyExist("audio_language_list", $mediaInfo["general"]);
        $vod->totalSize = $mediaInfo["general"]["file_size"]["bit"] / 1000000;

        if($mediaInfo["audios"]){
            $vod->audioCodec = $mediaInfo["audios"][0]["format"]["shortName"];
            $vod->audioBitrate = $mediaInfo["audios"][0]["bit_rate"]["absoluteValue"];
            $vod->audioBitrateTxt = $mediaInfo["audios"][0]["bit_rate"]["textValue"];
        }

        return $this->save($vod);
    }

    /**
     * Check if array key exist if exist then return value else return empty string
     * @param $key
     * @param $array
     * @return string
     */
    public function getValueIfArrayKeyExist($key, $array)
    {
        return (array_key_exists($key, $array))? $array[$key]: "";
    }

    /**
     * @param $id
     * @param $contentType
     * @param $bucket
     * @param $provider
     * @param null $vodId
     * @return Vod|bool
     */
    public function initialize($id, $contentType, $bucket, $provider = "intacker", $vodId = null)
    {
        $data = $this->redisClient->get(getenv("VOD_REDIS_KEY_PREFIX").$id);
        if(empty($data)){
            return false;
        }

        $data = json_decode($data, true);
        if($provider == "b2"){
            $originalFileUrl = $this->getVodUrlB2($data["path"]);
        } else{
            $this->awsS3Util->setInTackerClient();
            $originalFileUrl = $this->getVodUrlInTackerUrl($data["path"]);
        }

        if($vodId){
            $vod = $this->get($vodId);
        } else {
            $vod = $this->initVod($data["filename"], $data["basename"], $data["extension"], $originalFileUrl, $data["path"], $bucket);
        }

        $queueUrl = $this->awsSqsUtil->getQueueUrl(getenv("AWS_MEDIA_INFO_SQS_QUEUE_NAME"));

        if(getenv("AWS_MEDIA_INFO_SQS_QUEUE_TYPE") === "fifo"){
            $this->awsSqsUtil->sendMessageFifo($queueUrl ,
                json_encode([
                    "vodId"=>$vod->getId(),
                    "originalFileUrl"=> $originalFileUrl,
                    "originalFilePath"=> $data["path"],
                    "bucket"=> $bucket,
                    "originalExtension"=> $vod->originalExtension,
                    "contentType" => $contentType
                ]),
                "vod-id-{$vod->getId()}-". uniqid()
            );
        } else{
            $this->awsSqsUtil->sendMessage($queueUrl ,
                json_encode([
                    "vodId"=>$vod->getId(),
                    "originalFileUrl"=> $originalFileUrl,
                    "originalFilePath"=> $data["path"],
                    "bucket"=> $bucket,
                    "originalExtension"=> $vod->originalExtension,
                    "contentType" => $contentType
                ])
            );
        }

        $this->redisClient->del(getenv("VOD_REDIS_KEY_PREFIX").$id);
        return $vod;
    }

    /**
     * @param Vod $vod
     * @return Vod
     */
    public function save(Vod $vod)
    {
        $this->entityManager->persist($vod);
        $this->entityManager->flush();
        return $vod;
    }

    /**
     * @param $path
     * @param bool $bucket
     * @return string
     */
    public function getVodUrlB2($path, $bucket = false)
    {
        if(!$bucket){
            $bucket =  getenv("B2_VOD_UPLOAD_BUCKET");
        }

        $bucket = $this->awsS3Util->getBucketName($bucket);
        $region = getenv("B2_REGION");
        return "https://{$bucket}.s3.{$region}.backblazeb2.com/{$path}";
    }

    /**
     * @param $path
     * @return string
     */
    public function getVodUrlInTackerUrl($path)
    {
        $this->awsS3Util->setInTackerClient();
        $bucket = $this->awsS3Util->getBucketName(getenv("INTACKER_UPLOAD_BUCKET"));
        return getenv("INTACKER_ENDPOINT")."/{$bucket}/{$path}";
    }

    /**
     * @param Vod $vod
     * @param string $contentType
     */
    public function sendForTranscoding(Vod $vod, $contentType = "source_video"){

        $queueUrl = $this->awsSqsUtil->getQueueUrl(getenv("AWS_TRANSCODING_SQS_QUEUE_NAME"));

        $message =   json_encode([
            "vodId"=>$vod->getId(),
            "content_type" => $contentType
        ]);

        $messageGroupId = "vodId-{$vod->getId()}-". uniqid();

        if(getenv("AWS_TRANSCODING_SQS_QUEUE_TYPE") === "fifo"){
            $this->awsSqsUtil->sendMessageFifo($queueUrl , $message , $messageGroupId);
        } else{
            $this->awsSqsUtil->sendMessage($queueUrl , $message);
        }
    }

    /**
     * @param $vodPath
     * @return mixed
     */
    public function getMediaInfo($vodPath)
    {
        $mediaInfo = new MediaInfo();

//        $mediaInfoContainer = $mediaInfo->getInfo("https://ivnews-preprod.s3.us-west-002.backblazeb2.com/Big_Buck_Bunny_1080_10s_1MB.mp4");
//        $mediaInfoContainer = $mediaInfo->getInfo("https://ivnews-preprod.s3.us-west-002.backblazeb2.com/ABC7-News-5-00PM-(2004)-2020-09-04-17-00-00-ABC7-News-5-00PM.ts");
        $mediaInfoContainer = $mediaInfo->getInfo($vodPath);

        $general = $this->prepareMediaInfoData($mediaInfoContainer->getGeneral());
        $videos = $this->prepareMediaInfoData($mediaInfoContainer->getVideos(), true);
        $audios = $this->prepareMediaInfoData($mediaInfoContainer->getAudios(), true);
        $subTitles = $this->prepareMediaInfoData($mediaInfoContainer->getSubtitles(), true);

        $result = [
            "general" => $general,
            "videos" => array_key_exists(0,$videos) ? $videos[0] : [] ,
            "audios" => $audios,
            "subTitles" => $subTitles,
        ];

        return json_decode(json_encode($result), true);

    }

    /**
     * @param Vod $vod
     * @return array
     */
    public function prepareSQSMsg(Vod $vod)
    {
        return [
            "vod_path" => $vod->originalFilePath
        ];
    }

    /**
     * @param $collection
     * @param bool $isArrayCollection
     * @return array
     */
    public function prepareMediaInfoData($collection, $isArrayCollection = false)
    {

        if(!$isArrayCollection){
            return $this->mediaInfoToArray($collection);
        } else {
            $res = [];
            foreach ($collection as $item) {
                $res[] =  $this->mediaInfoToArray($item);
            }
            return $res;
        }
    }


    /**
     * @param $collection
     * @return array
     */
    private function mediaInfoToArray($collection)
    {
        $availableInfo = $collection->list();
        $res = [];
        foreach ($availableInfo as $key) {
            $res[$key] = $collection->get($key);
        }
        return $res;
    }

    /**
     * @param Vod $vod
     * @param $bucketName
     * @return array
     */
    public function getPreSignedUrl(Vod $vod, $bucketName)
    {
        $filePath = $vod->videoPath;
        $vodPathInfo = pathinfo($vod->videoPath);

        
        $bucketName = $this->awsS3Util->getBucketName($bucketName);
       

        if ($vod->story || $vod->interstitial) {

            $this->awsS3Util->setWasabiClient();
            $urls["url"] = $this->awsS3Util->getObjectUrl($bucketName, $filePath);

            $this->awsS3Util->setWasabiClient();
            $urls["url"] = $this->awsS3Util->getObjectUrl($bucketName, $filePath);

            $vodPathInfo = pathinfo($vod->videoPath);
            $filename = $vodPathInfo["basename"].".vtt";
            $filePath = str_replace("/hls", "", "{$vodPathInfo["dirname"]}/$filename" );
            $urls["ccUrl"] =  $this->awsS3Util->getObjectUrl($bucketName, $filePath);

        }
        
        if ($vod->sourceVideo && $vod->sourceVideo->uploadedType == SourceUploadTypeEnum::NAS) {
            $filePath = $vod->originalFileUrl;
            
            $this->awsS3Util->setClient($_ENV["AWS_S3_NAS_ACCESS_KEY"],$_ENV["AWS_S3_NAS_SECRET_KEY"], $_ENV["SOURCE_VIDEO_CDN_URL"]);
            $path  = explode("/hls/index.m3u8",$filePath);
            $bucketName = $_ENV["NAS_SYNC_BUCKET"];
            $urls["url"] = $this->awsS3Util->getObjectUrl($bucketName, $filePath);
            
            $vodPathInfo = pathinfo($vod->originalFilePath);
            
            $filePath = $path[0]."/output.vtt";
            
            $urls["ccUrl"] = $this->awsS3Util->getObjectUrl($bucketName, $filePath);

            $filePath = $path[0]."/output.mp3";
            $urls["audioUrl"] =  $this->awsS3Util->getObjectUrl($bucketName, $filePath);
            
            $filePath = $path[0]."/audioanalysis.json";
            $urls["audioanalysis"] =  $this->awsS3Util->getObjectUrl($bucketName, $filePath);
            
        } else if ($vod->sourceVideo) {

            $this->awsS3Util->setWasabiClient();
            $urls["url"] = $this->awsS3Util->getObjectUrl($bucketName, $filePath);

            $this->awsS3Util->setS3Client(getenv("SOURCE_VIDEO_CDN_URL"));
            $sourcePath = explode('/', $filePath);
            array_shift($sourcePath);
            $sourcePath = implode("/", $sourcePath);
            $urls["url"] = $this->awsS3Util->getObjectUrl($this->awsS3Util->getBucketName(getenv("AWS_S3_TRANSCODING_OUTPUT_BUCKET")), $sourcePath);
            $this->awsS3Util->setWasabiClient();
            $vodPathInfo = pathinfo($vod->originalFilePath);
            $vttFileName = "{$vodPathInfo["filename"]}-close-caption-{$vod->getId()}.mp4.vtt";
            $vodFilePathInfo = pathinfo($vod->videoPath);
            $filePath = str_replace("/hls", "", "{$vodFilePathInfo["dirname"]}/$vttFileName" );
            $urls["ccUrl"] = $this->awsS3Util->getObjectUrl($bucketName, $filePath);

            $vodPathInfo = pathinfo($vod->originalFilePath);
            $audioFileName = "{$vodPathInfo["filename"]}-audio-{$vod->getId()}.mp4";
            $vodFilePathInfo = pathinfo($vod->videoPath);

            $filePath = str_replace("/hls", "", "{$vodFilePathInfo["dirname"]}/$audioFileName" );
            $urls["audioUrl"] =  $this->awsS3Util->getObjectUrl($bucketName, $filePath);
        }

        return $urls;

    }

    public function replaceDomain($url , $domain){
        $components = parse_url( $url);
        return  str_replace($components['host'], getenv("B2_CDN_URL"), $url);
    }
}
