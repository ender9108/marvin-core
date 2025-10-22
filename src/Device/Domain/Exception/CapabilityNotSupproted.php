<?php

namespace Marvin\Device\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Device\Domain\ValueObject\CapabilityType;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;

class CapabilityNotSupproted extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        string $code,
        public readonly ?string $capability = null,
    ) {
        parent::__construct($message, $code);
    }

    public static function withCapabilityType(CapabilityType $capabilityType): self
    {
        return new self(
            sprintf('The capability %s is not supported', $capabilityType->value),
            'D00001',
            $capabilityType->value,
        );
    }

    public function translationId(): string
    {
        if (null !== $this->capability) {
            return 'device.exceptions.capability_not_supported_with_capability_type';
        }

        return 'device.exceptions.capability_not_supported';
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
