<?php

namespace Marvin\Domotic\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Domotic\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Reference;
use Override;

final class ProtocolNotFound extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $id = null,
        public readonly ?string $reference = null,
    ) {
        parent::__construct($message);
    }

    public static function withId(ProtocolId $id): self
    {
        return new self(
            sprintf('Protocol with id %s was not found', $id->toString()),
            $id->toString(),
        );
    }

    public static function withReference(Reference $reference): self
    {
        return new self(
            sprintf('Protocol with reference %s was not found', $reference->value),
            null,
            $reference->value
        );
    }

    #[Override]
    public function translationId(): string
    {
        if (null !== $this->id) {
            return 'domotic.exceptions.protocol_not_found_with_id';
        }
        return 'domotic.exceptions.protocol_not_found';
    }

    #[Override]
    /** @return array<string, string|null> */
    public function translationParameters(): array
    {
        return [
            '%id%' => $this->id,
        ];
    }

    #[Override]
    public function translationDomain(): string
    {
        return 'domotic';
    }
}
