<?php

namespace App\Controller;

use App\Entity\Interstitial;
use App\Entity\TranscodingQueue;
use App\Entity\Vod;
use App\Enums\TranscodingContentTypeEnum;
use App\Enums\TranscodingQueueStatusEnum;
use App\Services\InterstitialService;
use App\Services\SourceVideoService;
use App\Services\StoryService;
use App\Services\TranscodingQueueService;
use App\Services\UploadService;
use App\Services\VodService;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class UploadController extends AbstractController
{
    /**
     * @Route("/vods/pre_sign", name="vod_upload_presign")
     * @param Request $request
     * @param UploadService $uploadService
     * @return JsonResponse
     */
    public function index(Request $request , UploadService $uploadService)
    {
        $params = json_decode($request->getContent(), true);

        $params['source'] = isset($params['source']) ? $params['source'] : null;
        $params['interstitial_id'] = isset($params['interstitial_id']) ? $params['interstitial_id'] : null;
        if(empty($params["source"])){
            return $this->json(["message"=> "Invalid Source"], 400);
        }
        $pathInfos = pathinfo($params["source"]);
        $params = array_merge($pathInfos, $params);
        $res = $uploadService->token($params,"vod");
        return $this->json($res);
    }

    /**
     * @Route("/vods/retranscode/{id}", name="vod_retranscoding")
     *
     * @param $id
     * @param Request $request
     * @param VodService $vodService
     * @param SourceVideoService $sourceVideoService
     * @param InterstitialService $interstitialService
     * @return JsonResponse
     */
    public function reTranscode($id , Request $request , VodService $vodService, SourceVideoService $sourceVideoService, StoryService $storyService, InterstitialService $interstitialService)
    {
        $vod = $vodService->get($id);

        if(empty($vod)){
            return $this->json(["message" => "Vod Not Found", 400]);
        }

        if(!empty($vod->sourceVideo)){
            $sourceVideoService->sendForTranscoding($vod->sourceVideo);
        } else if(!empty($vod->story)){
            $storyService->sendForTranscoding($vod->story);
        } else if(!empty($vod->interstitial)){
            $interstitialService->sendForTranscoding($vod->interstitial);
        }

        return $this->json(["message" => "vod $id sent for transcoding"]);
    }

    /**
     * @Route("/vods/upload", name="vod_upload")
     * @param Request $request
     * @param UploadService $uploadService
     * @param KernelInterface $kernel
     * @param VodService $vodService
     * @param SourceVideoService $sourceVideoService
     * @return JsonResponse|Response
     */
    public function post(Request $request, UploadService $uploadService, KernelInterface $kernel, VodService $vodService, SourceVideoService $sourceVideoService)
    {

        try {

            /** @var UploadedFile $file */
            $file = $request->files->get('source');
            $contentType = $request->get("contentType");

            if (!empty($file)) {
                if (!preg_match('/(video)/', $file->getMimeType())) {
                    return new JsonResponse(["message" => "Invalid File " . $file->getMimeType()], 400);
                }

                $params = pathinfo($file->getClientOriginalName());

                $tmpDir = $kernel->getProjectDir() . '/public/tmp/vods';
                $tmpFileName = Uuid::uuid4().".".$file->getClientOriginalExtension();
                $localPath = "{$tmpDir}/{$tmpFileName}";
                $file->move($tmpDir, $localPath);
                $responseData = $uploadService->uploadToB2($localPath , $params);

                if($responseData){
                    unlink($localPath);
                   $vod =  $vodService->initialize($responseData["name"], $contentType);
                   if($vod){
                       if($contentType == TranscodingContentTypeEnum::SOURCE_VIDEO){
                           $res =  $sourceVideoService->init($vod);
                           $vod->sourceVideo = $res;
                           $vodService->save($vod);
                           return $this->json($res);
                       }
                   } else{
                       return $this->json(["message"=> "Invalid Redis Key"], 400);
                   }

                } else{
                    return $this->json($responseData);
                }

            } else {
                return new JsonResponse(["message" => "input field is required"], 400);
            }

        } catch (\Exception $e) {
            return new Response($e->getMessage());
        }
    }


    /**
     * @param $contentType
     * @param $request Request
     * @param VodService $vodService
     * @param SourceVideoService $sourceVideoService
     * @return JsonResponse
     * @Route("/vods/initialize/{contentType}", name="vod_initialize")
     */
    public function initializeUpload($contentType, Request $request ,VodService $vodService, SourceVideoService $sourceVideoService, InterstitialService $interstitialService)
    {
        $data = json_decode($request->getContent(), true);
        $id = $data["vod_id"];
        $interstitialId = (array_key_exists('interstitial_id',$data)) ? $data["interstitial_id"] : null ;
        $vodId = null;
        if($interstitialId){
            $interstitial =  $interstitialService->get($interstitialId);
            if (!empty($interstitial->vod)) {
                $vodId = $interstitial->vod->getId();
            }
        }
        $contentType = strtoupper($contentType);
        $vod =  $vodService->initialize($id, $contentType, getenv("INTACKER_UPLOAD_BUCKET"), "intacker", $vodId);
        if($vod){
            if($contentType == TranscodingContentTypeEnum::SOURCE_VIDEO){
                $res =  $sourceVideoService->init($vod);
                $vod->sourceVideo = $res;
                $vodService->save($vod);
                return $this->json($res);
            }
            if($contentType == TranscodingContentTypeEnum::INTERSTITIAL){
                /** @var Interstitial $interstitial */
                $interstitial =  $interstitialService->get($interstitialId);
                $interstitial->vod = $vod;
                $vod->interstitial = $interstitial;
                $interstitialService->save($interstitial);
                $vodService->save($vod);
                $response =  [
                    "id" => $interstitial->getId(),
                    "vod" => $interstitial->vod
                ];
                return $this->json($response);
            }
        } else{
            return $this->json(["message"=> "Invalid Redis Key"], 400);
        }
    }

    /**
     * @Route("/vods/tq/{id}/status/{status}", name="vod_transcoding_queue_update")
     */
    public function updateTranscodingQueue($id , $status,TranscodingQueueService $transcodingQueueService)
    {
        /** @var TranscodingQueue|null $transcodingQueue */
        $transcodingQueue = $transcodingQueueService->findByAwsJobId($id);

        if(empty($transcodingQueue)){
           return $this->json(["message"=> "job not found"], 400);
        }

        if(!in_array($status, TranscodingQueueStatusEnum::getConstants())){
            return $this->json(["message"=> "Invalid Status"], 400);
        }

        /**
         * check if current job status is not complete error or canceled then update status else do nothing
         * */
        if( !in_array($transcodingQueue->status, [
            TranscodingQueueStatusEnum::COMPLETE,
            TranscodingQueueStatusEnum::ERROR,
            TranscodingQueueStatusEnum::CANCELED ])){
            $transcodingQueue->status = $status;
            $transcodingQueueService->save($transcodingQueue);
        }

        return $this->json($transcodingQueue);
    }

    /**
     * @Route("/vods/{id}/pre_signed", name="vod_pre_signed")
     *
     * @param $id
     * @param Request $request
     * @param VodService $vodService
     * @return JsonResponse
     */
    public function getPreSignedUrl($id, Request $request, VodService $vodService)
    {
        $preSignedUrl = "";
        /**@var Vod $vod */
        $vod = $vodService->get($id);
        
        $response = [
            "url" => "",
            "ccUrl" => "",
            "audioUrl" => "",
        ];

        if($vod->status == "READY" ) {
            $response = $vodService->getPreSignedUrl($vod, getenv("VOD_BUCKET"));
        } 
        return $this->json($response);
    }

}
