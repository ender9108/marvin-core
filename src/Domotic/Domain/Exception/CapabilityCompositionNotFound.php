<?php

namespace Marvin\Domotic\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Domotic\Domain\ValueObject\Identity\CapabilityCompositionId;
use Override;

final class CapabilityCompositionNotFound extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $id = null,
    ) {
        parent::__construct($message);
    }

    public static function withId(CapabilityCompositionId $id): self
    {
        return new self(
            sprintf('CapabilityComposition with id %s was not found', $id->toString()),
            $id->toString(),
        );
    }

    #[Override]
    public function translationId(): string
    {
        if (null !== $this->id) {
            return 'domotic.exceptions.capability_composition_not_found_with_id';
        }
        return 'domotic.exceptions.capability_composition_not_found';
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
