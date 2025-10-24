<?php

namespace Marvin\Device\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Device\Domain\ValueObject\Capability;
use Marvin\Shared\Domain\ValueObject\Label;

class CapabilityNotFound extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        string $code,
        public readonly ?string $capability = null,
        public readonly ?string $deviceLabel = null,
    ) {
        parent::__construct($message, $code);
    }

    public static function withCapabilityAndDevice(
        Capability $capability,
        Label $deviceLabel
    ): self {
        return new self(
            sprintf('Capability %s not found on device %s', $capability->value, $deviceLabel->value),
            'D00013',
            $capability->value,
            $deviceLabel->value,
        );
    }

    public function translationId(): string
    {
        if (null !== $this->capability && null !== $this->deviceLabel) {
            return 'device.exceptions.capability_not_found_with_capability_name_and_device_label';
        }

        return 'device.exceptions.capability_not_found';
    }

    public function translationParameters(): array
    {
        return [
            '%capability_name%' => $this->capability,
            '%device_label%' => $this->deviceLabel,
        ];
    }

    public function translationDomain(): string
    {
        return 'device';
    }
}
