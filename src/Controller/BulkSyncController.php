<?php

namespace App\Controller;

use App\Model\Message;
use App\Util\AwsSqsUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class BulkSyncController extends AbstractController
{

    /**
     * @Route("/bulk/sync", name="bulk_sync")
     */
    public function bulkSync(Request $request, AwsSqsUtil $awsSqsUtil): Response
    {
        $data = json_decode($request->getContent(), true);
        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        $startDate = (\array_key_exists('startDate', $data)) ? $data['startDate'] : null;
        $endDate = (\array_key_exists('endDate', $data)) ? $data['endDate'] : "";
        $contentType = (\array_key_exists('contentType', $data)) ? $data['contentType'] : "";

        if (empty($startDate) || empty($endDate) || empty($contentType)) {

            return new JsonResponse(['message' => sprintf('startDate or endDate or contentType is empty')], 400);
        }
            $queueUrl = $awsSqsUtil->getQueueUrl($_ENV["BULK_SYNC_SQS_QUEUE_NAME"]);

            $awsSqsUtil->sendMessage($queueUrl ,
                json_encode([
                    "contentType" => $contentType,
                    "startDate" => $startDate,
                    "endDate" => $endDate
                ])
            );
        return new JsonResponse(["message" => "successfully send in a queue"]);
    }
}
