<?php

namespace Marvin\Security\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Security\Domain\ValueObject\Identity\RequestResetPasswordId;
use Override;

final class RequestResetPasswordAlreadyExists extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    #[Override]
    public function translationId(): string
    {
        return 'security.exceptions.request_reset_password_already_exists';
    }

    #[Override]
    /** @return array<string, string> */
    public function translationParameters(): array
    {
        return [];
    }

    #[Override]
    public function translationDomain(): string
    {
        return 'security';
    }
}
