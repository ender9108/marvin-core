<?php

namespace Marvin\Secret\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;

final class EncryptionError extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        string $code,
    ) {
        parent::__construct($message, $code);
    }

    public function translationId(): string
    {
        return 'secret.exceptions.encryption_error';
    }

    public function translationParameters(): array
    {
        return [];
    }

    public function translationDomain(): string
    {
        return 'secret';
    }
}
