<?php

namespace App\Controller;

use App\Entity\Segment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SegmentController extends AbstractController
{
    /**
     * @Route("/segment/vne", name="segment_vne")
     */
    public function createSegment(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        $vneId = (\array_key_exists('vneId', $data)) ? $data['vneId'] : null;
        $vneTitle = (\array_key_exists('vneTitle', $data)) ? $data['vneTitle'] : null;
        $title = (\array_key_exists('title', $data)) ? $data['title'] : null;

        if (empty($vneId) && empty($vneTitle) && empty($title)) {
            return new JsonResponse(['message' => sprintf('VneId or VneTitle or Title are empty.')], 400);
        }

        $vneIdExist = $entityManager->getRepository(Segment::class)->findOneBy(['vneId' => $vneId]);

        if (!empty($vneIdExist)) {
            return new JsonResponse(['message' => sprintf("Segment Already Exist With vneId!")], 400);
        }

        $segment = new Segment();
        $segment->vneId = $vneId;
        $segment->vneTitle = $vneTitle;
        $segment->title = $title;

        $entityManager->persist($segment);
        $entityManager->flush();

        return new JsonResponse(['message' => sprintf('Segment %s successfully created', $segment->getId())], 201);
    }


    /**
     * @Route("/segment/vne/update/{vne_id}", name="segment_vne_update")
     */
    public function updateSegment($vne_id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        $vneTitle = (\array_key_exists('vneTitle', $data)) ? $data['vneTitle'] : null;
        $title = (\array_key_exists('title', $data)) ? $data['title'] : null;

        if (empty($vneTitle) && empty($title)) {
            return new JsonResponse(['message' => sprintf('VneTitle or Title are empty.')], 400);
        }

        /** @var Segment $segment */
        $segment = $entityManager->getRepository(Segment::class)->findOneBy(["vneId" => $vne_id]);

        $segment->vneTitle = $vneTitle;
        $segment->title = $title;

        $entityManager->persist($segment);
        $entityManager->flush();

        return new JsonResponse(['message' => sprintf('Segment %s successfully updated', $segment->getId())], 201);
    }
}
