<?php

namespace App\Controller;

use ApiPlatform\Core\JsonLd\Action\ContextAction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProfileController extends AbstractController
{
    /**
     * @param Request $request
     * @param NormalizerInterface $decorated
     * @return JsonResponse
     *
     * @Route("/profile", name="profile")
     * @Route("/end_user/profile", name="profile_end_user")
     *
     */
    public function index(Request $request, NormalizerInterface $decorated)
    {
        $member =  $this->getUser();
        
        $memberArray = $decorated->normalize($member, "jsonld");
        unset($memberArray["password"]);
        return new JsonResponse($memberArray, Response::HTTP_OK);
    }
}
