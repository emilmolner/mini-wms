<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Transforma erorile din /api in raspunsuri JSON scurte: {"eroare": "mesajul"}.
 * InvalidArgumentException (date gresite trimise de client) devine 400 Bad Request.
 */
#[AsEventListener]
final class ApiExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        if (!str_starts_with($event->getRequest()->getPathInfo(), '/api')) {
            return;
        }

        $e = $event->getThrowable();
        $status = match (true) {
            $e instanceof HttpExceptionInterface => $e->getStatusCode(),
            $e instanceof \InvalidArgumentException => 400,
            default => 500,
        };

        $event->setResponse(new JsonResponse(['eroare' => $e->getMessage()], $status));
    }
}
