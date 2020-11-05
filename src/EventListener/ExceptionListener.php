<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener {
    public function onKernelException(ExceptionEvent $event) {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            $message = [
                'error' => $exception->getMessage(),
                'code' => $exception->getStatusCode()
            ];
            $response = new JsonResponse($message);

            $response->setStatusCode($exception->getStatusCode());
        } else {
            $message = [
                'error' => $exception->getMessage(),
                'code' => 500,
            ];
            $response = new JsonResponse($message);
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $event->setResponse($response);
        
    }
}