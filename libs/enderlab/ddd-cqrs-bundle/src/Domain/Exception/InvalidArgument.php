<?php

declare(strict_types=1);

namespace EnderLab\DddCqrsBundle\Domain\Exception;

use EnderLab\DddCqrsApiPlatformBundle\Domain\Exception\UnprocessableInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\Interfaces\TranslatableExceptionInterface;
use Override;

class InvalidArgument extends DomainException implements TranslatableExceptionInterface, UnprocessableInterface
{
    public function __construct(
        string $translationId,
        array $parameters = [],
        ?string $code = null,
    ) {
        parent::__construct($translationId, $code);
        $this->translationParams = $parameters;
    }
    #[Override]
    public function translationId(): string
    {
        return $this->translationId;
    }
    #[Override]
    public function translationParameters(): array
    {
        return $this->translationParams;
    }
    #[Override]
    public function translationDomain(): string
    {
        return 'assert_messages';
    }
}
