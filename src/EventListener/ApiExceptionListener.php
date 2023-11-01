<?php
namespace App\EventListener;

use App\Exception\ApiJsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ApiExceptionListener {

    public function onKernelException(ExceptionEvent $event)
    {

        $exception = $event->getThrowable();
        if ($exception instanceof ApiJsonException) {
            $response = new JsonResponse(['code' => $exception->getCode(), 'message' => $exception->getMessage()], $exception->getCode());
            $event->setResponse($response);
        }
    }
}