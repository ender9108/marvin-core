<?php

namespace Marvin\Security\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Security\Domain\ValueObject\Identity\UserId;
use Marvin\Shared\Domain\ValueObject\Email;
use Override;

final class LastUserAdmin extends DomainException implements TranslatableExceptionInterface
{
    #[Override]
    public function translationId(): string
    {
        return 'security.exceptions.last_user_type';
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
