<?php

namespace App\Controller;

use App\Entity\LineUp;
use App\Entity\LineUpStories;
use App\Entity\Story;
use App\Services\Vne\LineupService;
use App\Services\VneService;
use Doctrine\ORM\EntityManagerInterface;
use PhpCsFixer\DocBlock\Line;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LineUpController extends AbstractController
{
    /**
     * @Route("/request/line_up", name="app_lineup_request", methods={"POST","HEAD"})
     */ 
    public function index(Request $request, EntityManagerInterface $entityManager, LineupService $lineupService, VneService $vneService): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        $firstLineUp = (\array_key_exists('firstLineUp', $data)) ? $data['firstLineUp'] : null;
        $longitude = (\array_key_exists('longitude', $data)) ? $data['longitude'] : null;
        $latitude = (\array_key_exists('latitude', $data)) ? $data['latitude'] : null;
        $requestedAt = (\array_key_exists('requestedAt', $data)) ? $data['requestedAt'] : '23/05/2013 01:20:20';


        if (empty($firstLineUp) || empty($latitude) || empty($longitude) || empty($requestedAt) ) {
            return new JsonResponse(['message' => sprintf('firstLineUp or longitude or latitude or requestedAt are empty.')], 400);
        }

        $lineup = new LineUp();
        $lineup->firstLineUp = $firstLineUp;
        $lineup->longitude = $longitude;
        $lineup->latitude = $latitude;
        $lineup->ipAddress = $request->getClientIp();
        $requestedAtDateTime = new \DateTime();
        $lineup->requestedAt = $requestedAtDateTime->createFromFormat('d/m/Y H:i:s', $requestedAt);
        $hour = (int) $requestedAtDateTime->format('H');

        $hourRange = "";
        if( $hour > 6 && $hour <= 11) {
            $hourRange = "MORNING";
        }
        else if($hour > 11 && $hour <= 16) {
            $hourRange = "AFTERNOON";
        }
        else if($hour > 16 && $hour <= 23) {
            $hourRange = "EVENING";
        }

        $entityManager->persist($lineup);
        $entityManager->flush();

        $vneService->loadService("LineupService");
        /** @var LineupService $lineupService */
        $vneLineupService = $vneService->service;
        $response = $lineupService->post($lineup->getId());

        // $response = '{"lineup_id": "test_lineup_id1", "segments": [{"story_id": "1786", "segment": "TOP STORIES"}, {"story_id": "1787", "segment": "TOP STORIES"}, {"story_id": "1816", "segment": "USA "}, {"story_id": "1798", "segment": "USA "}, {"story_id": "1769", "segment": "USA "}, {"story_id": "1758", "segment": "USA "}, {"story_id": "1754", "segment": "USA "}, {"story_id": "1746", "segment": "USA "}, {"story_id": "1745", "segment": "USA "}, {"story_id": "1738", "segment": "USA "}, {"story_id": "0000", "segment": "Free ad"}, {"story_id": "0001", "segment": "Basic ad"}, {"story_id": "1807", "segment": "WEATHER FORECAST"}, {"story_id": "1704", "segment": "INTERNATIONAL "}, {"story_id": "1698", "segment": "INTERNATIONAL "}, {"story_id": "0002", "segment": "Free+ ad"}, {"story_id": "1665", "segment": "COMMENT"}], "segment_count": [{"segment": "TOP STORIES", "count": 2}, {"segment": "USA ", "count": 8}, {"segment": "Free ad", "count": 1}, {"segment": "Basic ad", "count": 1}, {"segment": "WEATHER FORECAST", "count": 1}, {"segment": "INTERNATIONAL ", "count": 2}, {"segment": "Free+ ad", "count": 1}, {"segment": "COMMENT", "count": 1}], "status": "successful"}';
        $response = json_decode($response, false);
        
        $lineup->lineUpContentJson = $response;
        foreach ($response->segments as $segment ) {
            $lastSegment =  "";
            foreach ($segment["segments"] as $storyData){

               if(array_key_exists("story_id", $storyData)) {
                    $stories = $entityManager->getRepository(Story::class)->find($storyData["story_id"]);
                    if ($stories) {
                        $lineup->stories[] = $stories;
                    }
                }
            }
        }

        $entityManager->persist($lineup);
        $entityManager->flush();

        return new JsonResponse($lineup->getId());

    }
}
