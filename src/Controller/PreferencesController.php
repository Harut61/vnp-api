<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Services\Vne\PreferencesService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\EndUser;

/**
 * @Route("/user_pref")
 * Class PreferencesController
 * @package App\Controller
 */
class PreferencesController extends AbstractController
{
    /**
     * @Route("/highlevel_subject", name="preferences_highlevel_subject")
     * @param Request $request
     * @return JsonResponse
     * @param PreferencesService $preferencesService
     */
    public function setHighLevelPref(Request $request, PreferencesService $preferencesService): Response
    {
        $body = json_decode($request->getContent());

        if (empty($body)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        /** @var EndUser $endUser */
        $endUser = $this->getUser();
        $endUserId = $endUser->getId();
        $pref = (array_key_exists('pref', $body)) ? $body->pref : null;
        $notPref = (array_key_exists('notPref', $body)) ? $body->notPref : null;

        if (empty($pref)) {
            return new JsonResponse(['message' => sprintf('Prefered choices are not provided')], 400);
        }
        if (empty($notPref)) {
            return new JsonResponse(['message' => sprintf('Un-Preferred choices are not provided')], 400);
        }

        $response = $preferencesService->setHighLevelPref(
            $endUserId,
            $pref,
            $notPref
        );
        return $this->json($response);
    }

    /**
     * @Route("/subject", name="preferences_subject")
     * @param Request $request
     * @return JsonResponse
     * @param PreferencesService $preferencesService
     */
    public function setSubjectPref(Request $request, PreferencesService $preferencesService): Response
    {
        $body = json_decode($request->getContent());

        if (empty($body)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        /** @var EndUser $endUser */
        $endUser = $this->getUser();
        $endUserId = $endUser->getId();
        $pref = (array_key_exists('pref', $body)) ? $body->pref : null;
        $notPref = (array_key_exists('notPref', $body)) ? $body->notPref : null;

        
        if (empty($pref)) {
            return new JsonResponse(['message' => sprintf('Prefered choices are not provided')], 400);
        }
        if (empty($notPref)) {
            return new JsonResponse(['message' => sprintf('Un-Preferred choices are not provided')], 400);
        }

        $response = $preferencesService->setSubjectPref(
            $endUserId,
            $pref,
            $notPref
        );
        return $this->json($response);
    }

    /**
     * @Route("/people_organisation", name="preferences_people_organisation")
     * @param Request $request
     * @return JsonResponse
     * @param PreferencesService $preferencesService
     */
    public function setPeopleOrganisationPref(Request $request, PreferencesService $preferencesService): Response
    {
        $body = json_decode($request->getContent());

        if (empty($body)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        /** @var EndUser $endUser */
        $endUser = $this->getUser();
        $endUserId = $endUser->getId();
        $pref = (array_key_exists('pref', $body)) ? $body->pref : null;
        $notPref = (array_key_exists('notPref', $body)) ? $body->notPref : null;

        
        if (empty($pref)) {
            return new JsonResponse(['message' => sprintf('Prefered choices are not provided')], 400);
        }
        if (empty($notPref)) {
            return new JsonResponse(['message' => sprintf('Un-Preferred choices are not provided')], 400);
        }

        $response = $preferencesService->setPeopleOrganizationPref(
            $endUserId,
            $pref,
            $notPref
        );
        return $this->json($response);
    }

    /**
     * @Route("/source_entity", name="preferences_source_entity")
     * @param Request $request
     * @return JsonResponse
     * @param PreferencesService $preferencesService
     */
    public function setSourceEntityPref(Request $request, PreferencesService $preferencesService): Response
    {
        $body = json_decode($request->getContent());

        if (empty($body)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        /** @var EndUser $endUser */
        $endUser = $this->getUser();
        $endUserId = $endUser->getId();
        $pref = (array_key_exists('pref', $body)) ? $body->pref : null;
        $notPref = (array_key_exists('notPref', $body)) ? $body->notPref : null;

        
        if (empty($pref)) {
            return new JsonResponse(['message' => sprintf('Prefered choices are not provided')], 400);
        }
        if (empty($notPref)) {
            return new JsonResponse(['message' => sprintf('Un-Preferred choices are not provided')], 400);
        }

        $response = $preferencesService->setSourceEntityPref(
            $endUserId,
            $pref,
            $notPref
        );
        return $this->json($response);
    }

    /**
     * @Route("/location", name="preferences_location")
     * @param Request $request
     * @return JsonResponse
     * @param PreferencesService $preferencesService
     */
    public function setLocationPref(Request $request, PreferencesService $preferencesService): Response
    {
        $body = json_decode($request->getContent());

        if (empty($body)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        /** @var EndUser $endUser */
        $endUser = $this->getUser();
        $endUserId = $endUser->getId();
        $pref = (array_key_exists('pref', $body)) ? $body->pref : null;
        $notPref = (array_key_exists('notPref', $body)) ? $body->notPref : null;

        
        if (empty($pref)) {
            return new JsonResponse(['message' => sprintf('Prefered choices are not provided')], 400);
        }
        if (empty($notPref)) {
            return new JsonResponse(['message' => sprintf('Un-Preferred choices are not provided')], 400);
        }

        $response = $preferencesService->setLocationPref(
            $endUserId,
            $pref,
            $notPref
        );
        return $this->json($response);
    }

    /**
     * @Route("/lineup_length", name="preferences_lineup_length")
     * @param Request $request
     * @return JsonResponse
     * @param PreferencesService $preferencesService
     */
    public function setLineupLengthPref(Request $request, PreferencesService $preferencesService): Response
    {
        $body = json_decode($request->getContent());

        if (empty($body)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        /** @var EndUser $endUser */
        $endUser = $this->getUser();
        $endUserId = $endUser->getId();
        $pref = (array_key_exists('pref', $body)) ? $body->pref : null;

        
        if (empty($pref)) {
            return new JsonResponse(['message' => sprintf('Prefered length is not provided')], 400);
        }

        $response = $preferencesService->setLineupLengthPref(
            $endUserId,
            $pref
        );
        return $this->json($response);
    }

    /**
     * @Route("/personal_interest", name="preferences_personal_interest")
     * @param Request $request
     * @return JsonResponse
     * @param PreferencesService $preferencesService
     */
    public function setPersonalInterestPref(Request $request, PreferencesService $preferencesService): Response
    {
        $body = json_decode($request->getContent());

        if (empty($body)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        /** @var EndUser $endUser */
        $endUser = $this->getUser();
        $endUserId = $endUser->getId();
        $pref = (array_key_exists('pref', $body)) ? $body->pref : null;

        
        if (empty($pref)) {
            return new JsonResponse(['message' => sprintf('Prefered length is not provided')], 400);
        }

        $response = $preferencesService->setPersonalInterestPref(
            $endUserId,
            $pref
        );
        return new JsonResponse(['message' => $response], 200);
    }

    /**
     * @Route("", name="user_pref")
     * @param Request $request
     * @param PreferencesService $preferencesService
     * @return JsonResponse
     */
    public function getUserPref(Request $request, PreferencesService $preferencesService): JsonResponse
    {
        /** @var EndUser $endUser */
        $endUser = $this->getUser();
        $endUserId = $endUser->getId();
        $currentUrl = $request->getPathInfo();

        $response = $preferencesService->index(
            $currentUrl,
            $request->get("page", 1),
            $request->get("itemPerPage", 10),
            $request->get("filter", ""),
            $endUserId
        );
        return $this->json($response);
    }

    /**
     * @Route("/all", name="user_pref_all")
     * @param Request $request
     * @param PreferencesService $preferencesService
     * @return JsonResponse
     */
    public function getUserPrefAll(Request $request, PreferencesService $preferencesService): JsonResponse
    {
        $body = json_decode($request->getContent());

        if (empty($body)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }
        $result = [];
        foreach ($body as $userId) {

            $currentUrl = $request->getPathInfo();

            $response = $preferencesService->index(
                $currentUrl,
                $request->get("page", 1),
                $request->get("itemPerPage", 10),
                $request->get("filter", ""),
                $userId
            );
            array_push($result, [$userId => $response]);
        }

        return $this->json($result);
    }
}
