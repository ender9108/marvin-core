<?php

namespace Marvin\System\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Shared\Domain\ValueObject\Reference;
use Marvin\System\Domain\ValueObject\Identity\PluginId;
use Override;

final class PluginRequirementMissing extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly array $references = [],
    ) {
        parent::__construct($message);
    }

    /**
     * @param array<Reference> $references
     * @return self
     */
    public static function withReferences(array $references): self
    {
        return new self(
            sprintf(
                'Plugin with reference %s already exists',
                implode(', ', array_map(fn (Reference $reference) => $reference->value, $references))
            ),
            $references,
        );
    }

    #[Override]
    public function translationId(): string
    {
        if (!empty($this->references)) {
            return 'system.exceptions.plugin_requirement_missing_with_references';
        }

        return 'system.exceptions.plugin_requirement_missing';
    }

    #[Override]
    /** @return array<string, string|null> */
    public function translationParameters(): array
    {
        return [
            '%reference%' => $this->reference->value,
        ];
    }

    #[Override]
    public function translationDomain(): string
    {
        return 'system';
    }
}
