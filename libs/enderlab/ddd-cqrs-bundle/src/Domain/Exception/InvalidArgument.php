<?php
namespace EnderLab\DddCqrsBundle\Domain\Exception;

use Override;

final class InvalidArgument extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        private readonly string $translationId,
        private readonly array $parameters,
    ) {
        parent::__construct($translationId);
    }

    #[Override]
    public function translationId(): string
    {
        return $this->translationId;
    }

    #[Override]
    public function translationParameters(): array
    {
        return $this->parameters;
    }

    #[Override]
    public function translationDomain(): string
    {
        return 'messages';
    }
}
