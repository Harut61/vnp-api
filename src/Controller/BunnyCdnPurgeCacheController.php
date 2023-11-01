<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BunnyCdnPurgeCacheController extends AbstractController
{
    /**
     * @Route("/bunny/cdn/purge/cache", name="bunny_cdn_purge_cache")
     */
    public function index(Request $request): Response
    {
        $curl = curl_init();

        $url = $request->get('url', null);

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.bunny.net/purge?url=$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => [
                "AccessKey: ".getenv('BUNNY_CDN_ACCESS_KEY')
            ],
        ]);


        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {

            return new JsonResponse(["message" => $err]);
        } else {

            return new JsonResponse(["message" => "successfully purged"]);

        }
    }
}
