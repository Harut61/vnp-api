<?php

namespace App\Controller;

use App\Entity\HighLevelSubjectTag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HighLevelSubjectTagController extends AbstractController
{
    /**
     * @Route("/high/level/subject/vne", name="high_level_subject_vne")
     */
    public function createHighLevelSubject(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        $vneId = (\array_key_exists('vneId', $data)) ? $data['vneId'] : null;
        $vneTitle = (\array_key_exists('vneTitle', $data)) ? $data['vneTitle'] : null;
        $titleForMarker = (\array_key_exists('titleForMarker', $data)) ? $data['titleForMarker'] : null;
        $titleForEndUser = (\array_key_exists('titleForEndUser', $data)) ? $data['titleForEndUser'] : null;

        if (empty($vneId) && empty($vneTitle)) {
            return new JsonResponse(['message' => sprintf('VneId or VneTitle are empty.')], 400);
        }

        if (empty($titleForMarker) && empty($titleForEndUser)) {
            return new JsonResponse(['message' => sprintf('titleForMarker or titleForEndUser are empty.')], 400);
        }

        $vneIdExist = $entityManager->getRepository(HighLevelSubjectTag::class)->findOneBy(['vneId' => $vneId]);

        if (!empty($vneIdExist)) {
            return new JsonResponse(['message' => sprintf("High Level Subject Tag Already Exist With vneId!")], 400);
        }

        $highLevelSubject = new HighLevelSubjectTag();
        $highLevelSubject->vneId = $vneId;
        $highLevelSubject->vneTitle = $vneTitle;
        $highLevelSubject->titleForMarker = $titleForMarker;
        $highLevelSubject->titleForEndUser = $titleForEndUser;

        $entityManager->persist($highLevelSubject);
        $entityManager->flush();

        return new JsonResponse(['message' => sprintf('High Level Subject Tag %s successfully created', $highLevelSubject->getId())], 201);
    }


    /**
     * @Route("/high/level/subject/vne/update/{vne_id}", name="high_level_subject_vne_update")
     */
    public function updateHighLevelSubject($vne_id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        $vneTitle = (\array_key_exists('vneTitle', $data)) ? $data['vneTitle'] : null;
        $titleForMarker = (\array_key_exists('titleForMarker', $data)) ? $data['titleForMarker'] : null;
        $titleForEndUser = (\array_key_exists('titleForEndUser', $data)) ? $data['titleForEndUser'] : null;

        if (empty($vneTitle) && empty($titleForMarker) && empty($titleForEndUser)) {
            return new JsonResponse(['message' => sprintf('VneTitle or titleForMarker or titleForEndUser are empty.')], 400);
        }

        /** @var HighLevelSubjectTag $highLevelSubject */
        $highLevelSubject = $entityManager->getRepository(HighLevelSubjectTag::class)->findOneBy(["vneId" => $vne_id]);

        $highLevelSubject->vneTitle = $vneTitle;
        $highLevelSubject->titleForMarker = $titleForMarker;
        $highLevelSubject->titleForEndUser = $titleForEndUser;

        $entityManager->persist($highLevelSubject);
        $entityManager->flush();

        return new JsonResponse(['message' => sprintf('High Level Subject Tag %s successfully updated', $highLevelSubject->getId())], 201);
    }
}
