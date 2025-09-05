<?php

namespace Marvin\Security\Domain\Exception;

use Marvin\Security\Domain\ValueObject\Identity\UserId;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Shared\Domain\ValueObject\Email;
use Override;

final class LastUserType extends DomainException implements TranslatableExceptionInterface
{
    #[Override]
    public function translationId(): string
    {
        return 'security.exceptions.last_user_type';
    }

    #[Override]
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
