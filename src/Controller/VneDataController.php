<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Services\Vne\VneDataService;
use App\Services\Vne\HighLevelSubjectService;
use App\Services\Vne\PreferencesService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\EndUser;
use Google\Protobuf\Internal\RepeatedFieldIter;

/**
 * @Route("/vne_data")
 * Class VneDataController
 * @package App\Controller
 */
class VneDataController extends AbstractController
{
    /**
     * @Route("/topics", name="vne_data_topics")
     * @param Request $request
     * @param HighLevelSubjectService $highLevelSubjectService
     * @return JsonResponse
     */
    public function getTopics(Request $request, HighLevelSubjectService $highLevelSubjectService): JsonResponse
    {
        $currentUrl = $request->getPathInfo();

        $response = $highLevelSubjectService->index(
            $currentUrl,
            $request->get("page", 1),
            $request->get("itemPerPage", 10),
            $request->get("filter", ""),
        );
        return $this->json($response);
    }

    /**
     * @Route("/subject", name="vne_data_subject")
     * @param Request $request
     * @param VneDataService $vneDataService
     * @return JsonResponse
     */
    public function getSubject(Request $request, VneDataService $vneDataService): JsonResponse
    {
        $body = json_decode($request->getContent());

        if (empty($body)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        $searchString = (array_key_exists('search', $body)) ? $body->search : null;

        $currentUrl = $request->getPathInfo();

        $response = $vneDataService->index(
            $currentUrl,
            $request->get("page", 1),
            $request->get("itemPerPage", 10),
            $request->get("filter", ""),
            $searchString,
            'Subject'
        );
        return $this->json($response);
    }

    /**
     * @Route("/people_organisation", name="vne_data_people_organisation")
     * @param Request $request
     * @param VneDataService $vneDataService
     * @return JsonResponse
     */
    public function getPeopleOrganisation(Request $request, VneDataService $vneDataService): JsonResponse
    {
        $body = json_decode($request->getContent());

        if (empty($body)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        $searchString = (array_key_exists('search', $body)) ? $body->search : null;

        $currentUrl = $request->getPathInfo();

        $response = $vneDataService->index(
            $currentUrl,
            $request->get("page", 1),
            $request->get("itemPerPage", 10),
            $request->get("filter", ""),
            $searchString,
            'People and Organization'
        );
        return $this->json($response);
    }

    /**
     * @Route("/location", name="vne_data_location")
     * @param Request $request
     * @param VneDataService $vneDataService
     * @return JsonResponse
     */
    public function getLocation(Request $request, VneDataService $vneDataService): JsonResponse
    {
        $body = json_decode($request->getContent());

        if (empty($body)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        $searchString = (array_key_exists('search', $body)) ? $body->search : null;

        $currentUrl = $request->getPathInfo();

        $response = $vneDataService->index(
            $currentUrl,
            $request->get("page", 1),
            $request->get("itemPerPage", 10),
            $request->get("filter", ""),
            $searchString,
            'Location'
        );
        return $this->json($response);
    }

    /**
     * @Route("/personal_interest", name="vne_data_personal_interest")
     * @param Request $request
     * @param VneDataService $vneDataService
     * @return JsonResponse
     */
    public function getPersonalInterest(Request $request, VneDataService $vneDataService): JsonResponse
    {
        $body = json_decode($request->getContent());

        if (empty($body)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        $searchString = (array_key_exists('search', $body)) ? $body->search : null;

        $currentUrl = $request->getPathInfo();

        $response = $vneDataService->index(
            $currentUrl,
            $request->get("page", 1),
            $request->get("itemPerPage", 10),
            $request->get("filter", ""),
            $searchString,
            'Keyword'
        );
        return $this->json($response);
    }

    /**
     * @Route("/neutral_topics", name="vne_data_neutral_topics")
     * @param Request $request
     * @param HighLevelSubjectService $highLevelSubjectService
     * @param PreferencesService $preferencesService
     * @return JsonResponse
     */
    public function getNeutralTopics(Request $request, HighLevelSubjectService $highLevelSubjectService, PreferencesService $preferencesService): JsonResponse
    {
        /** @var EndUser $endUser */
        $endUser = $this->getUser();
        $endUserId = $endUser->getId();

        $currentUrl = $request->getPathInfo();

        $allTopicsResponse = $highLevelSubjectService->index(
            $currentUrl,
            $request->get("page", 1),
            $request->get("itemPerPage", 10),
            $request->get("filter", ""),
        );

        $allTopicsRaw = $allTopicsResponse["hydra:member"];
        $allTopics = [];
        foreach ($allTopicsRaw as $topicRaw) {
            array_push($allTopics, $topicRaw["name"]);
        }

        $userPrefResonse = $preferencesService->index(
            $currentUrl,
            $request->get("page", 1),
            $request->get("itemPerPage", 10),
            $request->get("filter", ""),
            $endUserId
        );

        $prefTopics = $userPrefResonse["hydra:member"][0]["prefHighlevelSubject"];
        $prefTopicsArray = [];
        foreach ($prefTopics as $prefTopic) {
            array_push($prefTopicsArray, $prefTopic);
        }

        $notPrefTopics = $userPrefResonse["hydra:member"][0]["notPrefHighlevelSubject"];
        $notPrefTopicsArray = [];
        foreach ($notPrefTopics as $notPrefTopic) {
            array_push($notPrefTopicsArray, $notPrefTopic);
        }

        $neutralTopicsArray = [];
        foreach($allTopics as $topic) {
            if (!in_array($topic, $prefTopicsArray) && !in_array($topic, $notPrefTopicsArray)) {
                array_push($neutralTopicsArray, $topic);
            }
        }

        $response[] = ["hydra:member" => $neutralTopicsArray];
        return $this->json($response);
    }
}
