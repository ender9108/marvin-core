<?php
namespace EnderLab\DddCqrsBundle\Domain\Exception;

use Override;

final class AssertException extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        private readonly string $translationId,
        private readonly array $parameters = [],
        ?string $code = null,
    ) {
        /* Format CODE_ERREUR::::TRANSLATION_ID */
        $translationIdParts = explode('::::', $translationId);
        $code = null;

        if (count($translationIdParts) === 2) {
            $translationId = $translationIdParts[1];
            $code = $translationIdParts[0];
        }

        parent::__construct($translationId, $code);
        $this->internalCode = $code ?? self::UNKNOWN_ERROR_CODE;
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
