<?php

namespace Marvin\Security\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;

class UserIsDeleted extends DomainException implements TranslatableExceptionInterface
{
    public function translationId(): string
    {
        return 'security.exceptions.SC0016.user_deleted';
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
