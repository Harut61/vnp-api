<?php
namespace App\Services;

use App\Entity\SourceVideo;
use App\Entity\Story;
use App\Util\AwsS3Util;
use App\Util\B2Util;
use Aws\Credentials\Credentials;
use BackblazeB2\Client;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Aws\CommandInterface;
use Aws\S3\S3Client;
use Predis\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UploadService
{
    protected $b2AccessKey;

    protected $b2SecretKey;

    protected $b2Region;
    
    protected $b2Entrypoint;

    /** @var \Redis $redisClient  */
    protected $redisClient;

    protected $preSignExp;

    protected $b2VodUploadBucket;

    protected $b2Acl;

    protected $validator;

    /** @var  $awsS3Util AwsS3Util */
    protected $awsS3Util;

    public function __construct(ClientInterface $redisClient, ValidatorInterface $validator, AwsS3Util $awsS3Util)
    {

        $this->redisClient = $redisClient;
        $this->b2AccessKey = getenv("B2_MASTER_ACCESS_KEY");
        $this->b2SecretKey = getenv("B2_MASTER_SECRET_KEY");
        $this->b2Region = getenv("B2_REGION");
        $this->b2Entrypoint = getenv("B2_ENTRYPOINT");
        $this->preSignExp = getenv("PRE_SIGNED_EXP_TIME");
        $this->b2Acl= getenv("B2_ACL");
        $this->validator = $validator;
        $this->awsS3Util = $awsS3Util;
        $this->b2VodUploadBucket = $this->awsS3Util->getBucketName(getenv("B2_VOD_UPLOAD_BUCKET"));
    }

    public function uploadToB2($localPath, $params)
    {
        $b2Client = new B2Util(getenv("B2_MASTER_ACCESS_KEY"), getenv("B2_MASTER_SECRET_KEY"));
        $bucketName =$b2Client->getBucketName(getenv("B2_VOD_UPLOAD_BUCKET"));

        $key = "{$this->generateKey("vod",$params)}.{$params["extension"]}";


        $file = $b2Client->upload([
            'BucketName' => $bucketName,
            'FileName' => $key,
             'Body' => fopen($localPath, 'r')
        ]);

        $params['bucket'] = $this->b2VodUploadBucket;
        $params['path'] = $key;
        $params['basename'] = $params["filename"];

        $this->_saveFileConfigInCache($key, $params);
        return $file->jsonSerialize();
    }

    /**
     * @param $params
     * @param string $key_prefix
     * @return array|mixed
     *  Generate a presigned URL allowing one AWS S3 direct upload available for 4 hours
     */
    public function token($params, $key_prefix = "vod")
    {
        $params['callback_url'] = isset($params['callback_url']) ? $params['callback_url'] : null;

        $this->postValidation($params,$key_prefix);

        $params['filename'] = $params['source'];

        $key = "{$this->generateKey($key_prefix,$params)}.{$params["extension"]}";

        $this->awsS3Util->setInTackerClient();
        $params['bucket'] = getenv("INTACKER_UPLOAD_BUCKET");
        $params['path'] = $key;
        $bucket = $this->awsS3Util->getBucketName($params['bucket']);

        $uploadUrl = $this->awsS3Util->getPreSignedUrl($bucket, $key,"PutObject", $this->preSignExp);

        $this->_saveFileConfigInCache($key, $params);

        if (isset($params['token_js']) && $params['token_js'] == true) {
            $jsParams['key'] = $key;
            return $this->tokenJs($jsParams);
        }

        return array(
            'id' => $key,
            'url' => $uploadUrl,
            'curl-command' => "curl -T {$params['source']} '$uploadUrl'"
        );
    }

    /**
     * @param string $key_prefix
     * @param Story $story
     * @return array|mixed
     *  Generate a presigned URL allowing one AWS S3 direct upload available for 4 hours
     */
    public function tokenForWasabi(Story $story)
    {


        $vodPathInfo = pathinfo($story->vod->videoPath);
       
        $filename = $vodPathInfo["filename"].".vtt";
        $params['filename'] = $filename;
        $key = str_replace("/hls", "", "{$vodPathInfo["dirname"]}/$filename" );

        $this->awsS3Util->setWasabiClient("https://s3.us-east-1.wasabisys.com");
        $params['bucket'] = getenv("VOD_BUCKET");
        $params['path'] = $key;
        $bucket = $this->awsS3Util->getBucketName($params['bucket']);

        $uploadUrl = $this->awsS3Util->getPreSignedUrl($bucket, $key,"PutObject", $this->preSignExp);


        if (isset($params['token_js']) && $params['token_js'] == true) {
            $jsParams['key'] = $key;
            return $this->tokenJs($jsParams);
        }

        return array(
            'id' => $key,
            'url' => $uploadUrl,
            'curl-command' => "curl -T {$filename} '$uploadUrl'"
        );
    }

    /**
     * @param SourceVideo $sourceVideo
     * @return array
     *  Generate a presigned URL allowing one AWS S3 direct upload available for 4 hours
     */
    public function tokenForSourceVideoVneCC(SourceVideo $sourceVideo)
    {

        $vodPathInfo = pathinfo($sourceVideo->vod->originalFilePath);

        $filename = "audioanalysis.json";
        $params['filename'] = $filename;
        $key = str_replace("/hls", "", "{$vodPathInfo["dirname"]}/$filename" );

        $this->awsS3Util->setS3Client();
        $params['bucket'] = getenv("NAS_SYNC_BUCKET");
        $params['path'] = $key;
        $bucket = $params['bucket'];

        $uploadUrl = $this->awsS3Util->getPreSignedUrl($bucket, $key,"PutObject", $this->preSignExp);


        if (isset($params['token_js']) && $params['token_js'] == true) {
            $jsParams['key'] = $key;
            return $this->tokenJs($jsParams);
        }

        return array(
            'id' => $key,
            'url' => $uploadUrl,
            'curl-command' => "curl -T {$filename} '$uploadUrl'"
        );
    }

    public function tokenJs($params)
    {


        $algorithm = "AWS4-HMAC-SHA256";
        $service = "s3";
        $date = gmdate('Ymd\THis\Z');
        $shortDate = gmdate('Ymd');
        $requestType = "aws4_request";
        $expires = $this->preSignExp;
        $successStatus = '201';

        $scope = [
            $this->b2AccessKey,
            $shortDate,
            $this->b2Region,
            $service,
            $requestType
        ];
        $credentials = implode('/', $scope);

        $policy = [
            'expiration' => gmdate('Y-m-d\TG:i:s\Z', strtotime("+{$this->preSignExp} minutes")),
            'conditions' => [
                ['bucket' => $this->b2VodUploadBucket],
                ['acl' => $this->b2Acl],
                [
                    'starts-with',
                    '$key',
                    ''
                ],
                ['success_action_status' => $successStatus],
                ['x-amz-credential' => $credentials],
                ['x-amz-algorithm' => $algorithm],
                ['x-amz-date' => $date],
                ['x-amz-expires' => $expires],
            ]
        ];
        $base64Policy = base64_encode(json_encode($policy));

        // Signing Keys
        $dateKey = hash_hmac('sha256', $shortDate, 'AWS4' . $this->b2SecretKey, true);
        $dateRegionKey = hash_hmac('sha256', $this->b2Region, $dateKey, true);
        $dateRegionServiceKey = hash_hmac('sha256', $service, $dateRegionKey, true);
        $signingKey = hash_hmac('sha256', $requestType, $dateRegionServiceKey, true);

        // Signature
        $signature = hash_hmac('sha256', $base64Policy, $signingKey);


        $params["acl"] = $this->b2Acl;
        $params["success_action_status"] = $successStatus;
        $params["policy"] = $base64Policy;
        $params["X-amz-algorithm"] = $algorithm;
        $params["X-amz-credential"] = $credentials;
        $params["x-amz-date"] = $date;
        $params["x-amz-expires"] = $expires;
        $params["X-amz-signature"] = $signature;
        return $params;
    }


    ##########################################################################
    # private function
    ##########################################################################
    private function generateKey($prefix, $params){
        $random_key = Uuid::uuid4();
        $random_key = $random_key->toString();
        switch ($prefix) {
            case "img":
                return $prefix .'-' .$params['content_type'] .'-' .$params['element_type'] .'-' .$params['element_id'] .'-' . $random_key;
                break;
            case "subtitle":
                return $prefix .'-' .$params['element_type'] .'-' .$params['element_id'] .'-'. $random_key;
                break;
            case "vod":
                return $prefix .'-' . $random_key;
                break;
        }
    }


    private function postValidation($params, $type)
    {

        $moreParamsToCheck = array(
            'source' => $params['source'],
            'callback_url' => $params['callback_url'],
        );

        //Using constraint to validate our params like gentlemen
        $constraints = array(
            'source' => array(
                new Assert\Type('string'),
                new Assert\NotBlank(),
                new Assert\Length(array(
                    'min' => 2,
                    'minMessage' => 'Your source should contain {{ limit }} characters minimum'
                ))
            ),
            'callback_url' => array(
                new Assert\NotBlank(),
                new Assert\Length(array(
                    'max' => 255,
                    'maxMessage' => 'Your callback url cannot be longer than {{ limit }} characters'
                )),
                new Assert\Url()
            ),
        );

        if (isset($params['token_js']) && $params['token_js'] == true) {
            unset($moreParamsToCheck['callback_url']);
            unset($constraints['callback_url']);
        }

        $collectionConstraint = new Assert\Collection($constraints);
        $this->validateArray($collectionConstraint, $moreParamsToCheck);

    }

    /**
     * Store in redis the file params for further integration
     * @param $key
     * @param $params
     */
    private function _saveFileConfigInCache($key, $params)
    {
        unset($params['bid']);

        $redisKey = 'vod:upload.ivnews.com:' . $key;

        $this->redisClient->set($redisKey, json_encode($params));
        $this->redisClient->expire($redisKey, strtotime("now") + $this->preSignExp);
    }

    public function validateArray($collection, $array)
    {
        $errorList = $this->validator->validate($array, $collection);
        if (count($errorList) > 0) {
            return $errorList;
        }
        return [];
    }
}
