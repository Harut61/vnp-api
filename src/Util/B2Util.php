<?php
namespace App\Util;


use BackblazeB2\Client;

class B2Util extends Client
{

    public function __construct($accountId, $applicationKey, array $options = [])
    {
        parent::__construct($accountId, $applicationKey, $options);

    }


    public function getUploadUrl($bucketName)
    {
        $bucketName = $this->getBucketName($bucketName);
        $bucketId = $this->getBucketIdFromName($bucketName);

        $response = $this->sendAuthorizedRequest('POST', 'b2_get_upload_url', [
            'bucketId' => $bucketId,
        ]);
        return $response;
    }

    public function getPreSignedUrl($bucketName, $filePath, $valid = 3600){
        $bucketName = $this->getBucketName($bucketName);
        $bucketId = $this->getBucketIdFromName($bucketName);

        $response = $this->sendAuthorizedRequest('POST', 'b2_get_download_authorization', [
            'bucketId' => $bucketId,
            'validDurationInSeconds' => $valid,
            'fileNamePrefix' => $filePath
        ]);
        $authToken = $response["authorizationToken"];
        $url = "/file/$bucketName/$filePath?Authorization=$authToken";
        return $url;
    }

    public function getAuthToken($bucketName, $filePath, $valid = 3600){
        $bucketName = $this->getBucketName($bucketName);
        $bucketId = $this->getBucketIdFromName($bucketName);

        $response = $this->sendAuthorizedRequest('POST', 'b2_get_download_authorization', [
            'bucketId' => $bucketId,
            'validDurationInSeconds' => $valid,
            'fileNamePrefix' => $filePath
        ]);
        return $response["authorizationToken"];

    }

    public function getUrlFromToken($authToken, $bucketName, $filePath)
    {
        return "/file/$bucketName/$filePath?Authorization=$authToken";
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

}