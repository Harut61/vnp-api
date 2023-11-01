<?php

namespace App\Controller;

use App\Entity\Interstitial;
use App\Enums\StoryStatusEnum;
use App\Services\InterstitialService;
use App\Services\StoryService;
use App\Services\UploadService;
use App\Services\VodService;
use App\Util\AwsSqsUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/interstitial")
 */
class InterstitialController extends AbstractController
{

    /**
     * @Route("/{id}/pre_signed", name="interstitial_pre_signed")
     * @param $id
     * @param Request $request
     * @param InterstitialService $interstitialService
     * @param VodService $vodService
     * @return JsonResponse
     */
    public function getPreSignedUrl($id, Request $request, InterstitialService $interstitialService, VodService $vodService)
    {

        /** @var Interstitial $interstitial */
        $interstitial = $interstitialService->get($id);

        $response = [
            "url" => "",
            "ccUrl" => "",
            "audioUrl" => "",
        ];

        if(!empty($interstitial) && $interstitial->vod){
            if($interstitial->vod->status == "READY" ) {
                $response = $vodService->getPreSignedUrl($interstitial->vod, getenv("VOD_BUCKET"));
            }
        }

        return $this->json($response);
    }
}

