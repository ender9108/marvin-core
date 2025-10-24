<?php

namespace Marvin\Security\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;

class InvalidCurrentPassword extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(string $message)
    {
        parent::__construct($message);
        $this->internalCode = 'S00001';
    }

    public function translationId(): string
    {
        return 'security.exceptions.invalid_current_password';
    }

    /** @return array<string, string> */
    public function translationParameters(): array
    {
        return [];
    }

    public function translationDomain(): string
    {
        return 'security';
    }
}
