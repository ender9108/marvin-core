<?php
/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

namespace Marvin\Shared\Presentation\Exception\Service;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\Interfaces\TranslatableExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

final readonly class ExceptionMessageManager
{
    private const string ERROR_LIST_URL = 'https://github.com/ender9108/marvin-core/blob/main/docs/error_code/error_code.md';

    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function cliResponseFormat(Throwable $exception): array
    {
        return $this->buildBody($exception);
    }

    public function jsonResponseFormat(Throwable $exception): JsonResponse
    {
        return new JsonResponse(
            $this->buildBody($exception),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            ['Content-Type' => 'application/problem+json']
        );
    }

    private function buildBody(Throwable $exception): array
    {
        $message = $this->translatedMessage($exception);
        $body = [];

        if (
            $exception instanceof TranslatableExceptionInterface ||
            $exception instanceof DomainException
        ) {
            $parts = explode('.', $exception->translationId());
            $codeName = '#'.$exception->getInternalCode().'_'.end($parts);

            $body = [
                'type' => self::ERROR_LIST_URL.$codeName,
                'title' => '#'.end($parts),
                'detail' => $message,
            ];
        } else {
            $body = [
                'type' => self::ERROR_LIST_URL.'#uncknown_error-E999',
                'title' => '#uncknown_error-E999',
                'detail' => $message,
            ];
        }

        if (true === $this->isDebugMode()) {
            $body['debug'] = $exception->getTraceAsString();
        }

        return $body;
    }

    private function translatedMessage(Throwable $exception): string
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
        return false; //$this->parameters->get('kernel.debug');
    }
}
