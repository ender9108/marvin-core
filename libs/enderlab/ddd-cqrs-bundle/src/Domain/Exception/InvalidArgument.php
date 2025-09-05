<?php
namespace EnderLab\DddCqrsBundle\Domain\Exception;

use Override;

final class InvalidArgument extends DomainException implements TranslatableExceptionInterface
{
    #[Override]
    public function translationId(): string
    {
        return 'shared.exceptions.invalid_argument';
    }

    #[Override]
    public function translationParameters(): array
    {
        return [];
    }

    #[Override]
    public function translationDomain(): string
    {
        return 'messages';
    }
}
