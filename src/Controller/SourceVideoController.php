<?php

namespace App\Controller;

use App\Entity\SourceVideo;
use App\Entity\Vod;
use App\Enums\SourceUploadTypeEnum;
use App\Enums\SourceVideoStatusEnum;
use App\Services\SourceVideoService;
use App\Services\UploadService;
use App\Services\VodService;
use App\Util\AwsSqsUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SourceVideoController extends AbstractController
{
    /**
     * @Route("/source_videos/hard_delete/{id}", name="source_video_delete")
     * @param AwsSqsUtil $awsSqsUtil
     * @return JsonResponse
     */
    public function delete($id ,AwsSqsUtil $awsSqsUtil): JsonResponse
    {
        $queueUrl = $awsSqsUtil->getQueueUrl(getenv("SOURCE_VIDEO_DELETE_SQS_QUEUE_NAME"));

        if(getenv("AWS_MEDIA_INFO_SQS_QUEUE_TYPE") === "fifo"){
            $awsSqsUtil->sendMessageFifo($queueUrl ,
                json_encode([
                    "sourceVideoId"=>$id
                ]),
                "source-video-id-$id-". uniqid()
            );
        } else{
            $awsSqsUtil->sendMessage($queueUrl ,
                json_encode([
                    "sourceVideoId"=>$id,
                ])
            );
        }

        return new JsonResponse(["message" => "successfully send in a queue"]);

    }

    /**
     * @Route("/source_videos/{id}/pre_signed", name="source_videos_pre_signed")
     * @param $id
     * @param Request $request
     * @param SourceVideoService $sourceVideoService
     * @param VodService $vodService
     * @return JsonResponse
     */
    public function getPreSignedUrl($id, Request $request, SourceVideoService $sourceVideoService, VodService $vodService)
    {

        /**@var SourceVideo $sourceVideo */
        $sourceVideo = $sourceVideoService->get($id);

        $response = [
            "url" => "",
            "ccUrl" => "",
            "audioUrl" => "",
        ];

        if($sourceVideo->vod->status == "READY" ) {
            $bucket = getenv("VOD_BUCKET");
            if($sourceVideo->uploadedType == SourceUploadTypeEnum::NAS){
                $bucket = $sourceVideo->vod->originalFileBucket;
            } 
            
            $response = $vodService->getPreSignedUrl($sourceVideo->vod, $bucket);
        }
        return $this->json($response);
    }

    /**
     * @Route("/source_videos/cc_pre_sign/{id}", name="source_videos_cc_upload_presign")
     * @param $id
     * @param Request $request
     * @param UploadService $uploadService
     * @param SourceVideoService $sourceVideoService
     * @return JsonResponse
     */
    public function index($id, Request $request , UploadService $uploadService, SourceVideoService $sourceVideoService)
    {

        $sourceVideo = $sourceVideoService->get($id);

        if(empty($sourceVideo)){
            return $this->json(["message"=> "Invalid Source Video"], 400);
        }

        $res = $uploadService->tokenForSourceVideoVneCC($sourceVideo);
        return $this->json($res);
    }

}
