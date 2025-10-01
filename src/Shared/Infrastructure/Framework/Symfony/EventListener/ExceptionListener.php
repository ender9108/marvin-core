<?php

namespace Marvin\Shared\Infrastructure\Framework\Symfony\EventListener;

use Marvin\Shared\Infrastructure\Framework\Symfony\Service\ExceptionMessageManager;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(KernelEvents::EXCEPTION, priority: -1)]
final readonly class ExceptionListener
{
    public function __construct(
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $response = $this->exceptionMessageManager->jsonResponseFormat($exception);
        $event->setResponse($response);
    }
}
