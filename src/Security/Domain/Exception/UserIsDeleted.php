<?php

namespace Marvin\Security\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;

class UserIsDeleted extends DomainException implements TranslatableExceptionInterface
{
    public function translationId(): string
    {
        return 'security.exceptions.user_deleted';
    }

    public function translationParameters(): array
    {
        return [];
    }

    public function translationDomain(): string
    {
        return 'security';
    }
}
