<?php

namespace App\Controller;

use App\Entity\SourceVideo;
use App\Entity\StoryType;
use App\Services\Vne\EndUserService;
use App\Services\Vne\LineupService;
use App\Services\Vne\NewsMarketService;
use App\Services\Vne\ShowService;
use App\Services\Vne\SourceService;
use App\Services\Vne\SourceVideoService;
use App\Services\Vne\StoryService;
use App\Services\Vne\StoryTypeService;
use App\Services\Vne\StoryTypeServices;
use App\Services\VneService;
use App\Util\AwsS3Util;
use Aws\S3\S3Client;
use Doctrine\ORM\EntityManagerInterface;
use Endpoints\Events\AddSourceReply;
use Endpoints\Events\AddSourceRequest;
use Endpoints\Events\AddStoryReply;
use Endpoints\Events\AddStoryRequest;
use Endpoints\Events\EventsClient;
use Endpoints\Events\GetCountiesReply;
use Endpoints\Events\GetCountiesRequest;
use Endpoints\Events\GetDMAReply;
use Endpoints\Events\GetDMAReplyHelper;
use Endpoints\Events\GetDMARequest;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\RepeatedFieldIter;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Endpoints\Events\PingRequest;
use Grpc\ChannelCredentials;
use Grpc\GrpcException;

/**
 * @Route("/vne")
 * Class VneTestController
 * @package App\Controller
 */
class VneTestController extends AbstractController
{
    /**
     * @Route("/sources")
     * @param SourceService $sourceService
     * @return JsonResponse
     */
    public function index(Request $request, SourceService $sourceService): JsonResponse
    {
        $currentUrl = $request->getPathInfo();

        $response = $sourceService->index(
            $currentUrl,
            $request->get("page", 1),
            $request->get("itemPerPage", 10),
            $request->get("filter", "")
        );
        return $this->json($response);



    }

    private function handleResponse(array $response)
    {
        echo "<pre>";
        /** @var GetDMAReply $response */
        $response = $response[0];
        /** @var RepeatedField $list */
        $list = $response->getDmaList();
        /** @var RepeatedFieldIter  $lists */
        $dmaList = $list->getIterator();
        $result = [];
        /** @var GetDMAReplyHelper $dma */
        foreach ($dmaList as $dma){
            $result[] = [
               "dmaName" => $dma->getDmaName()
            ];
        }
        return $result;

        /** @var AddSourceReply $response */
//        $response =  $response[0];
//        var_dump($response->getMessage());
//        var_dump($response->getStatus());

//        var_dump($response->getStoryId());
//        echo json_encode(json_decode(json_encode($response), true));

    }


    /**
     * @Route("/story-type", name="api_get_index_story_type_lists")
     * @param Request $request
     * @param VneService $vneService
     * @return JsonResponse
     */
    public function getStoryType(Request $request, VneService $vneService): JsonResponse
    {
        $vneService->loadService("StoryTypeService");
        /** @var StoryTypeService $storyTypeService */
        $storyTypeService = $vneService->service;

        $currentUrl = $request->getPathInfo();

        $response = $storyTypeService->index(
            $currentUrl,
            $request->get("page", 1),
            $request->get("itemPerPage", 10),
            $request->get("filter", "")
        );
        return $this->json($response);
    }


    /**
     * @Route("/source/add/{id}")
     * @return JsonResponse
     */
    public function addSource($id, Request $request, EntityManagerInterface $em, VneService $vneService  ): JsonResponse
    {
        $vneService->loadService("SourceService");
        /** @var SourceService $sourceService */
        $sourceService = $vneService->service;
        $response = $sourceService->post($id);
        return $this->json($response);
    }

    /**
     * @Route("/source/edit/{id}")
     * @return JsonResponse
     */
    public function editSource($id, Request $request, EntityManagerInterface $em, VneService $vneService  ): JsonResponse
    {
        $vneService->loadService("SourceService");
        /** @var SourceService $sourceService */
        $sourceService = $vneService->service;
        $response = $sourceService->put($id);
        return $this->json($response);
    }

    /**
     * @Route("/source/video/add/{id}")
     * @return JsonResponse
     */
    public function addSourceVideo($id, SourceVideoService $sourceVideoService, EntityManagerInterface $entityManager): JsonResponse
    {
        $apiClient = new Client();

        /** @var SourceVideo $sourceVideo */
        $sourceVideo = $entityManager->getRepository(SourceVideo::class)->findOneBy(['id' => $id]);

        $postParam = [
            "vnp_id" => $id,
            "show_id" => "{$sourceVideo->show->getId()}",
            "cc_file_link" => "testCClink",
            "audio_file_link" => "testAUDIOlink",
            "publication_datetime" => $sourceVideoService->formatDateTime($sourceVideo->publicationDate),
            "show_duration" => $sourceVideo->show->showDuration,
            "fps" => $sourceVideo->vod->videoFps
        ];

        $response = $sourceVideoService->postRequest("/add_source_video", $postParam);
        return $this->json($response);
    }

    /**
     * @Route("/source/video/edit/{id}",  methods={"GET", "POST"})
     * @return JsonResponse
     */
    public function editSourceVideo($id, Request $request, SourceVideoService $sourceVideoService, EntityManagerInterface $entityManager): JsonResponse
    {

        /** @var SourceVideo $sourceVideo */
        $sourceVideo = $entityManager->getRepository(SourceVideo::class)->findOneBy(['id' => $id]);

        $postParam = [
            "vnp_id" => $id,
            "changed_show_id" => "{$sourceVideo->show->getId()}",
            "changed_cc_file_link" => "testCClink",
            "changed_audio_file_link" => "testAUDIOlink",
            "changed_publication_datetime" => $sourceVideoService->formatDateTime($sourceVideo->publicationDate),
            "changed_show_duration" => $sourceVideo->show->showDuration,
            "changed_fps" => $sourceVideo->vod->videoFps
        ];
        $response = $sourceVideoService->patchRequest("/edit_source_video", $postParam);

        return $this->json($response);
    }

    /**
     * @Route("/show/add/{id}")
     * @return JsonResponse
     */
    public function addShow($id, VneService $vneService  ): JsonResponse
    {
        $vneService->loadService("ShowService");
        /** @var ShowService $showService */
        $showService = $vneService->service;
        $response = $showService->post($id);
        return $this->json($response);
    }

    /**
     * @Route("/show/edit/{id}")
     * @return JsonResponse
     */
    public function editShow($id, Request $request, EntityManagerInterface $em, VneService $vneService  ): JsonResponse
    {
        $vneService->loadService("ShowService");
        /** @var ShowService $showService */
        $showService = $vneService->service;
        $response = $showService->put($id);
        return $this->json($response);
    }

    /**
     * @Route("/story/add/{id}")
     * @return JsonResponse
     */
    public function addStory($id, VneService $vneService  ): JsonResponse
    {
        $vneService->loadService("StoryService");
        /** @var StoryService $storyService */
        $storyService = $vneService->service;
        $response = $storyService->post($id);
        return $this->json($response);
    }

    /**
     * @Route("/user/add/{id}")
     * @return JsonResponse
     */
    public function addUser($id, VneService $vneService  ): JsonResponse
    {
        $vneService->loadService("EndUserService");
        /** @var EndUserService $endUserService */
        $endUserService = $vneService->service;
        $response = $endUserService->post($id);
        return $this->json($response);
    }

    /**
     * @Route("/lineup/add/{id}")
     * @return JsonResponse
     */
    public function addlineup($id, VneService $vneService  ): JsonResponse
    {
        $vneService->loadService("LineupService");
        /** @var LineupService $lineupService */
        $lineupService = $vneService->service;
        $response = $lineupService->post($id);
        return $this->json($response);
    }
}
