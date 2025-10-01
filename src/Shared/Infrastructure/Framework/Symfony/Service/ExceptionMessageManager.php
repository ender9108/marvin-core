<?php

namespace Marvin\Shared\Infrastructure\Framework\Symfony\Service;

use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ExceptionMessageManager
{
    public function __construct(
        private ParameterBagInterface $parameters,
        private TranslatorInterface $translator,
    ) {
    }

    public function cliResponseFormat(Exception $exception): array
    {
        return $this->buildBody($exception);
    }

    public function jsonResponseFormat(Exception $exception): JsonResponse
    {
        return new JsonResponse(
            $this->buildBody($exception),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            ['Content-Type' => 'application/problem+json']
        );
    }

    private function buildBody(Exception $exception): array
    {
        $message = $this->translatedMessage($exception);
        $body = [];

        if ($exception instanceof TranslatableExceptionInterface) {
            $body = [
                'type' => $exception->translationId(),
                'title' => $exception->translationId(),
                'detail' => $message,
            ];
        } else {
            $body = [
                'type' => $exception->getCode(),
                'title' => $exception->getMessage(),
                'detail' => $message,
            ];
        }

        return $body;
    }

    private function translatedMessage(Exception $exception): string
    {
        if ($exception instanceof TranslatableExceptionInterface) {
            return $this->translator->trans(
                $exception->translationId(),
                $exception->translationParameters(),
                $exception->translationDomain(),
            );
        }

        return $exception->getMessage();
    }

    private function isDebugMode(): bool
    {
        return $this->parameters->get('kernel.debug');
    }
}
