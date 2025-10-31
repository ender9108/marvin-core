<?php

namespace Marvin\Security\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Override;

final class LastUserAdmin extends DomainException implements TranslatableExceptionInterface
{
    #[Override]
    public function translationId(): string
    {
        return 'security.exceptions.SC0017.last_user_type';
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
