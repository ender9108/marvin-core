<?php
namespace EnderLab\DddCqrsBundle\Domain\Exception;

use Override;

final class InvalidArgument extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        private readonly string $translationId,
        private readonly array $parameters = [],
        string $code = 'unknown_error',
    ) {
        parent::__construct($translationId, $code);
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
        return 'assert_messages';
    }
}
