<?php

namespace Marvin\System\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Shared\Domain\ValueObject\Reference;
use Marvin\System\Domain\ValueObject\Identity\PluginId;
use Override;

final class PluginAlreadyExist extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?Reference $reference = null,
    ) {
        parent::__construct($message);
        $this->code = 'SY0003';
    }

    public static function withReference(Reference $reference): self
    {
        return new self(
            sprintf('Plugin with reference %s already exists', $reference->value),
            $reference,
        );
    }

    #[Override]
    public function translationId(): string
    {
        if (null !== $this->reference->value) {
            return 'system.exceptions.plugin_already_exist_with_reference';
        }

        return 'system.exceptions.plugin_already_exist';
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
