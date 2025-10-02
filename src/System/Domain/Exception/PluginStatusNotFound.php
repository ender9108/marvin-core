<?php

namespace Marvin\System\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Shared\Domain\ValueObject\Reference;
use Marvin\System\Domain\ValueObject\Identity\PluginStatusId;
use Override;

final class PluginStatusNotFound extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?PluginStatusId $id = null,
        public readonly ?Reference $reference = null,
    ) {
        parent::__construct($message);
    }

    public static function withId(PluginStatusId $id): self
    {
        return new self(
            message: sprintf('PluginStatus with id %s was not found', $id->toString()),
            id: $id,
        );
    }

    public static function withReference(Reference $reference): self
    {
        return new self(
            message: sprintf('PluginStatus with reference %s was not found', $reference),
            reference: $reference,
        );
    }

    #[Override]
    public function translationId(): string
    {
        if (null !== $this->id) {
            return 'system.exceptions.plugin_status_not_found_with_id';
        }

        if (null !== $this->reference) {
            return 'system.exceptions.plugin_status_not_found_with_reference';
        }

        return 'system.exceptions.plugin_status_not_found';
    }

    #[Override]
    /** @return array<string, string|null> */
    public function translationParameters(): array
    {
        return [
            '%id%' => $this->id->toString(),
            '%reference%' => $this->reference->value,
        ];
    }

    #[Override]
    public function translationDomain(): string
    {
        return 'system';
    }
}
