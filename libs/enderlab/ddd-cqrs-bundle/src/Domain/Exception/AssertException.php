<?php
namespace EnderLab\DddCqrsBundle\Domain\Exception;

use Override;

final class AssertException extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $translationId,
        array $parameters = [],
        ?string $code = null,
    ) {
        parent::__construct($translationId, $code);
        $this->transParams = $parameters;
    }

    #[Override]
    public function translationId(): string
    {
        return $this->transId;
    }

    #[Override]
    public function translationParameters(): array
    {
        return $this->transParams;
    }

    #[Override]
    public function translationDomain(): string
    {
        return $this->transDomain;
    }
}
