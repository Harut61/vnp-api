<?php

namespace App\Controller;

use App\Entity\Story;
use App\Enums\StoryStatusEnum;
use App\Services\StoryService;
use App\Services\UploadService;
use App\Services\Vne\SourceVideoService;
use App\Services\VodService;
use App\Util\AwsSqsUtil;
use Chrisyue\PhpM3u8\Facade\ParserFacade;
use Chrisyue\PhpM3u8\Stream\TextStream;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StoryController extends AbstractController
{

    /**
     * @Route("/story/cc_pre_sign/{id}", name="story_cc_upload_presign")
     * @param Request $request
     * @param UploadService $uploadService
     * @return JsonResponse
     */
    public function index($id, Request $request , UploadService $uploadService, StoryService $storyService)
    {

        $story = $storyService->get($id);

        if(empty($story)){
            return $this->json(["message"=> "Invalid Story"], 400);
        }

        $res = $uploadService->tokenForWasabi($story);
        return $this->json($res);
    }

    /**
     * @Route("/stories/hard_delete/{id}", name="story_delete")
     * @param AwsSqsUtil $awsSqsUtil
     * @return JsonResponse
     */
    public function delete($id ,AwsSqsUtil $awsSqsUtil, StoryService $storyService): JsonResponse
    {
        $story = $storyService->get($id);
        $story->setDeletedAt(new \DateTime());
        $storyService->save($story);

        $queueUrl = $awsSqsUtil->getQueueUrl(getenv("SOURCE_VIDEO_DELETE_SQS_QUEUE_NAME"));

        if(getenv("AWS_MEDIA_INFO_SQS_QUEUE_TYPE") === "fifo"){
            $awsSqsUtil->sendMessageFifo($queueUrl ,
                json_encode([
                    "storyId"=>$id
                ]),
                "story-id-$id-". uniqid()
            );
        } else{
            $awsSqsUtil->sendMessage($queueUrl ,
                json_encode([
                    "storyId"=>$id,
                ])
            );
        }

        return new JsonResponse(["message" => "successfully send in a queue"]);
    }

    /**
     * @Route("/stories/{id}/pre_signed", name="stories_pre_signed")
     * @param $id
     * @param Request $request
     * @param StoryService $storyService
     * @param VodService $vodService
     * @return JsonResponse
     */
    public function getPreSignedUrl($id, Request $request, StoryService $storyService, VodService $vodService, LoggerInterface $logger)
    {
        $logger->info("=========================== PERFORMANCE-DEBUG STORY PRESIGNED URL STARTS FOR {$id} ==========================");

        $logger->info("=========================== PERFORMANCE-DEBUG STORY GET {$id} START ==========================");
        /**@var StoryService $sourceVideo */
        $storyService = $storyService->get($id);
        $logger->info("=========================== PERFORMANCE-DEBUG STORY GET {$id} END ==========================");

        $response = [
            "url" => "",
            "ccUrl" => "",
            "audioUrl" => "",
        ];

        if($storyService->vod->status == "READY" ) {
            $logger->info("=========================== PERFORMANCE-DEBUG GET PRESIGNED URL {$id} START ==========================");
            $response = $vodService->getPreSignedUrl($storyService->vod, getenv("VOD_BUCKET"));
            $logger->info("=========================== PERFORMANCE-DEBUG GET PRESIGNED URL {$id} END ==========================");
        }

        $logger->info("=========================== PERFORMANCE-DEBUG STORY PRESIGNED URL END FOR {$id} ==========================");
        return $this->json($response);
    }

    /**
     * @Route("/stories/update_last_chunk/{id}", name="stories_update_last_chunk")
     * @param $id
     * @param Request $request
     * @param StoryService $storyService
     * @param VodService $vodService
     * @return JsonResponse
     */
    public function storyUpdateLastChunk($id, Request $request, StoryService $storyService, VodService $vodService, LoggerInterface $logger)
    {
       
        /**@var Story $story */
        $story = $storyService->get($id);
        $response = $vodService->getPreSignedUrl($story->vod, getenv("VOD_BUCKET"));
         $response["url"] = "http://localhost/ivnews/stories/2106/index.m3u8";

       if($response["url"]){
        $mediaPlaylist = $this->parseM3u8($response["url"]);
    
        if(array_key_exists("EXT-X-STREAM-INF", $mediaPlaylist)){
            $newFileName = $mediaPlaylist["EXT-X-STREAM-INF"][0]["uri"];
            $oldFileName = basename($response["url"]);
            $url = str_replace($oldFileName, $newFileName, $response["url"]);
            $mediaPlaylist = $this->parseM3u8($url);
            $story = $this->setLastChunkInfo($mediaPlaylist["mediaSegments"], $story);
            $storyService->save($story);   
        } else {
            $story = $this->setLastChunkInfo($mediaPlaylist["mediaSegments"], $story);
            $storyService->save($story);   
            }
       } 
       
        return $this->json($story);
    }

    /**
     * parse m3u8 playlist to array
     *
     * @param string $url
     * @return array|mix
     */
    public function parseM3u8($url)
    {
        $parser = new ParserFacade();
        $content =   file_get_contents($url);
          /**
         * @var ArrayObject
         */
        return $parser->parse(new TextStream($content));
    }
    /**
     * Set Story Last Chunk Info
     *
     * @param array|mix $mediaPlaylist
     * @param Story $story
     * @return Story
     */
    public function setLastChunkInfo($mediaPlaylist, Story $story)
    {
        $mediaSegments = $mediaPlaylist;
        $lastChumkNumber = count($mediaSegments);
        $lastChunk = end($mediaSegments);
            
        $duration = $lastChunk["EXTINF"]->getDuration();

        $story->lastChunkDuration = $duration;
        $story->lastChunkNumber = $lastChumkNumber;
        return $story;
    }
    
    /**
     * @Route("/vne/stories/details/{id}", name="vne_stories_details")
     * @param $id
     * @param Request $request
     * @param StoryService $storyService
     * @param VodService $vodService
     * @return JsonResponse
     */
    public function storyDetails($id, Request $request,SourceVideoService $sourceVideoService, StoryService $storyService, VodService $vodService, EntityManagerInterface $entityManager)
    {

        /** @var Story $story */

        $story = $entityManager->getRepository(Story::class)->findOneBy(['id' => $id]);

        $postParam = [
            "source_video_id" => $story->sourceVideo->getId(),
            "story_start_time" => $story->storyStartFrame,
            "story_end_time" => $story->storyEndFrame,
            "lede_end_time" => $story->ledeEndFrame,
            "Story_id" => $story->getId()
        ];

        $client = new CurlHttpClient();
        $response = $client->request('POST',"/story_details",$postParam);

        return $this->json($response);
    }
}

