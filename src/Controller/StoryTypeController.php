<?php

namespace App\Controller;

use App\Entity\HighLevelSubjectTag;
use App\Entity\StoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StoryTypeController extends AbstractController
{
    /**
     * @Route("/story/type/vne", name="story_type_vne")
     */
    public function createStoryType(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        $vneId = (\array_key_exists('vneId', $data)) ? $data['vneId'] : null;
        $vneTitle = (\array_key_exists('vneTitle', $data)) ? $data['vneTitle'] : null;
        $titleForMarker = (\array_key_exists('titleForMarker', $data)) ? $data['titleForMarker'] : null;
        $titleForEndUser = (\array_key_exists('titleForEndUser', $data)) ? $data['titleForEndUser'] : null;
        $title = (\array_key_exists('title', $data)) ? $data['title'] : null;

        if (empty($vneId) && empty($vneTitle)) {
            return new JsonResponse(['message' => sprintf('VneId or VneTitle are empty.')], 400);
        }

        if (empty($titleForMarker) && empty($titleForEndUser)) {
            return new JsonResponse(['message' => sprintf('titleForMarker or titleForEndUser are empty.')], 400);
        }

        $vneIdExist = $entityManager->getRepository(StoryType::class)->findOneBy(['vneId' => $vneId]);

        if (!empty($vneIdExist)) {
            return new JsonResponse(['message' => sprintf("Story Type Already Exist With vneId!")], 400);
        }

        $storyType = new StoryType();
        $storyType->vneId = $vneId;
        $storyType->vneTitle = $vneTitle;
        $storyType->titleForEndUser = $titleForEndUser;
        $storyType->titleForMarker = $titleForMarker;
        $storyType->title = $title;

        $entityManager->persist($storyType);
        $entityManager->flush();

        return new JsonResponse(['message' => sprintf('Story Type %s successfully created', $storyType->getId())], 201);
    }


    /**
     * @Route("/story/type/vne/update/{vne_id}", name="story_type_vne_update")
     */
    public function updateStoryType($vne_id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        $vneTitle = (\array_key_exists('vneTitle', $data)) ? $data['vneTitle'] : null;
        $titleForMarker = (\array_key_exists('titleForMarker', $data)) ? $data['titleForMarker'] : null;
        $titleForEndUser = (\array_key_exists('titleForEndUser', $data)) ? $data['titleForEndUser'] : null;
        $title = (\array_key_exists('title', $data)) ? $data['title'] : null;
        if (empty($vneTitle) && empty($apId)) {
            return new JsonResponse(['message' => sprintf('VneTitle are empty.')], 400);
        }
        if (empty($titleForMarker) && empty($titleForEndUser)) {
            return new JsonResponse(['message' => sprintf('titleForMarker or titleForEndUser are empty.')], 400);
        }

        /** @var StoryType $storyType */
        $storyType = $entityManager->getRepository(StoryType::class)->findOneBy(["vneId" => $vne_id]);

        $storyType->vneTitle = $vneTitle;
        $storyType->titleForEndUser = $titleForEndUser;
        $storyType->titleForMarker = $titleForMarker;
        $storyType->title = $title;

        $entityManager->persist($storyType);
        $entityManager->flush();

        return new JsonResponse(['message' => sprintf('Story Type %s successfully updated', $storyType->getId())], 201);
    }
}
