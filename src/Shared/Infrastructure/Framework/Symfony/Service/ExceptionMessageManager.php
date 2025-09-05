<?php
namespace Marvin\Shared\Infrastructure\Framework\Symfony\Service;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ExceptionMessageManager
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function getMessage(DomainException $exception): string
    {
        if ($exception instanceof TranslatableExceptionInterface) {
            return $this
                ->translator
                ->trans(
                    $exception->translationId(),
                    $exception->translationParameters(),
                    $exception->translationDomain(),
                )
            ;
        }

        return $exception->getMessage();
    }
}
