<?php

namespace Marvin\Security\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;

class UserIsLocaked extends DomainException implements TranslatableExceptionInterface
{
    public function translationId(): string
    {
        return 'security.exceptions.SC0014.user_locked';
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
