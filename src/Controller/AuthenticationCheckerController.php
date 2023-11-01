<?php

namespace App\Controller;

use App\Entity\AdminUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthenticationCheckerController extends AbstractController
{
    /**
     * @Route("/authentication/checker", name="authentication_checker")
     */
    public function index(JWTTokenManagerInterface $JWTManager): Response
    {
        return new JsonResponse(["message" => true]);
    }
}
