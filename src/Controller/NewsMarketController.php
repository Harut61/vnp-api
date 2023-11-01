<?php

namespace App\Controller;

use App\Services\Vne\NewsMarketService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Endpoints\Events\PingRequest;
use Grpc\ChannelCredentials;
use Grpc\GrpcException;

/**
 * @Route("/news_markets")
 * Class NewsMarketController
 * @package App\Controller
 */
class NewsMarketController extends AbstractController
{
    /**
     * @Route("", name="api_get_index_news_markets_lists")
     * @param Request $request
     * @param NewsMarketService $newsMarketService
     * @return JsonResponse
     */
    public function index(Request $request, NewsMarketService $newsMarketService): JsonResponse
    {
        $currentUrl = $request->getPathInfo();

        $response = $newsMarketService->index(
            $currentUrl,
            $request->get("page", 1),
            $request->get("itemPerPage", 10),
            $request->get("filter", "")
        );
        return $this->json($response);

    }
}
