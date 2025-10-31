<?php

namespace Marvin\Device\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;

final class InvalidCompositeStrategy extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        private readonly ?string $deviceId = null,
        private readonly ?string $strategy = null,
    ) {
        parent::__construct($message);
    }

    public static function forDevice(string $deviceId, string $strategy): self
    {
        return new self(
            sprintf('Invalid composite strategy %s for device %s', $strategy, $deviceId),
            $deviceId,
            $strategy,
        );
    }

    public function translationId(): string
    {
        if (null !== $this->deviceId && null !== $this->strategy) {
            return 'device.exceptions.DE0018.invalid_composite_strategy_with_device_id_and_strategy';
        }

        return 'device.exceptions.DE0019.invalid_composite_strategy';
    }

    public function translationParameters(): array
    {
        return [
            '%strategy%' => $this->strategy,
            '%device%' => $this->deviceId,
        ];
    }

    public function translationDomain(): string
    {
        return 'device';
    }
}
