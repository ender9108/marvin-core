<?php

namespace Marvin\Shared\Infrastructure\Framework\Symfony\EventListener;

use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(KernelEvents::EXCEPTION, priority: -1)]
final readonly class ExceptionListener
{
    public function __construct(
        private TranslatorInterface $translator
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof TranslatableExceptionInterface) {
            $message = $this->translator->trans(
                $exception->translationId(),
                $exception->translationParameters(),
                $exception->translationDomain(),
            );

            $response = new JsonResponse([
                'title' => $exception->translationId(),
                'detail' => $message,
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ], status: Response::HTTP_UNPROCESSABLE_ENTITY);

            $event->setResponse($response);
        }
    }
}
