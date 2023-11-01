<?php

namespace App\Util;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;

/**
 * Class S3
 * @package App\Utils\Aws
 */
class AwsS3Util
{
    /**
     * @var S3Client
     */
    private $client;


    public function __construct()
    {
        $this->setClient(
            getenv("B2_ACCESS_KEY"),
            getenv("B2_SECRET_KEY"),
            getenv("B2_ENTRYPOINT"),
            getenv("B2_REGION")
        );
    }

    /**
     * @return S3Client
     */
    public function setInTackerClient()
    {
        $credentials = new Credentials( getenv("INTACKER_ACCESS_KEY"), getenv("INTACKER_SECRET_KEY"));
        $this->client = new S3Client([
            'endpoint' => getenv("INTACKER_ENDPOINT"),
            'region' => 'us-east-1',
            'version' => '2006-03-01',
            'credentials' => $credentials,
            "s3BucketEndpoint"=> true,
            "use_path_style_endpoint"=> true
        ]);

        return $this->client;
    }

    public function setB2Client(){
        $this->client = $this->setClient(
            getenv("B2_ACCESS_KEY"),
            getenv("B2_SECRET_KEY"),
            getenv("B2_ENTRYPOINT"),
            getenv("B2_REGION")
        );
        return $this->client;
    }

    /**
     * @return S3Client
     */
    public function setWasabiClient($entrypoint = "")
    {
        $entrypoint = ($entrypoint) ? $entrypoint : getenv("WASABI_ENTRYPOINT");
        return $this->setClient(
            getenv("WASABI_ACCESS_KEY"),
            getenv("WASABI_SECRET_KEY"),
            $entrypoint,
            getenv("WASABI_REGION"),
            "latest"
        );
    }

    /**
     * @param string $entrypoint
     * @return S3Client
     */
    public function setS3Client($entrypoint = "")
    {
        return $this->setClient(
            getenv("AWS_S3_ACCESS_KEY"),
            getenv("AWS_S3_SECRET_KEY"),
            $entrypoint
        );
    }


    public function setB2ClientCDN(){
        $this->client = $this->setClient(
            getenv("B2_ACCESS_KEY"),
            getenv("B2_SECRET_KEY"),
            getenv("B2_CDN_URL"),
            getenv("B2_REGION")
        );
        return $this->client;
    }

    /**
     * @param $accessKey
     * @param $secretKey
     * @param string $entryPoint
     * @param string $region
     * @param string $version
     * @return S3Client
     */
    public function setClient($accessKey, $secretKey, $entryPoint = "" , $region = 'us-east-1', $version = 'latest')
    {

        $credentials = new Credentials($accessKey, $secretKey);
        $params = [
            'credentials' => $credentials,
            'region' => $region,
            'version' => $version,
            "s3BucketEndpoint"=> true,
            "use_path_style_endpoint"=> true
        ];

        if(!empty($entryPoint)) {
            $params['endpoint'] = $entryPoint;
        }

        $this->client = new S3Client($params);
        $this->client->registerStreamWrapper();

        return $this->client;
    }

    /**
     * @return S3Client
     * @throws \Exception
     */
    public function getClient()
    {
        if (!$this->client instanceof S3Client) {
            throw new \Exception("S3Client is not set");
        }
        return $this->client;
    }

    public function getBucketName($bucketName)
    {
        return strtolower(sprintf(
            '%s-%s-%s',
            strtoupper(getenv("APP_NAME")),
            strtoupper(getenv("IVN_ENV")),
            $bucketName
        ));
    }

    /**
     * @param $bucket
     * @param $key
     * @param string $operation
     * @param int $preSignExp
     * @return string
     */
    public function getPreSignedUrl($bucket, $key, $operation = "GetObject", $preSignExp = 120)
    {


        //Giving info to the AWS SDK, to create a presigned URL

        $command = $this->getClient()->getCommand($operation, array(
            'Bucket' => $bucket,
            'Key' => $key,
            'Body' => ''
        ));

        $request = $this->getClient()->createPresignedRequest($command,"+{$preSignExp} minutes"); // 2 hours
        return (string)$request->getUri();
    }

    /**
     * @param $bucket
     * @param $key
     * @param string $operation
     * @param int $preSignExp
     * @return string
     */
    public function getObjectUrl($bucket, $key)
    {

        return $this->getClient()->getObjectUrl($bucket, $key);
    }

    /**
     * @param $bucket
     * @param $path
     * @return string
     */
    public function getS3Url($bucket, $path){
        return "s3://$bucket/$path";
    }

    /**
     * @param $bucket
     * @param $path
     */
    public function deleteFolder($bucket, $path)
    {
        $this->getClient()->deleteMatchingObjects($bucket,"$path/");
    }

    /**
     * @param $path
     * @return bool
     */
    public function createPath($path) {
        $pathInfo = pathinfo($path);
        $path = $pathInfo["dirname"];
        if (is_dir($path)) return true;
        $prev_path = substr($path, 0, strrpos($path, '/', -2) + 1 );
        $return = $this->createPath($prev_path);
        return ($return && is_writable($prev_path)) ? mkdir($path) : false;
    }



}
