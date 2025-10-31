<?php

namespace Marvin\Device\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Device\Domain\ValueObject\Capability;

class CapabilityNotSupported extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $capability = null,
    ) {
        parent::__construct($message);
    }

    public static function withCapability(Capability $capability): self
    {
        return new self(
            sprintf('The capability %s is not supported', $capability->value),
            $capability->value,
        );
    }

    public function translationId(): string
    {
        if (null !== $this->capability) {
            return 'device.exceptions.DE0008.capability_not_supported_with_capability_type';
        }

        return 'device.exceptions.DE0007.capability_not_supported';
    }

    public function translationParameters(): array
    {
        return [
            '%capability%' => $this->capability
        ];
    }

    public function translationDomain(): string
    {
        return 'device';
    }
}
