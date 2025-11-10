<?php

namespace Marvin\Protocol\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

class DeviceTimeoutException extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $deviceId = null,
        public readonly ?float $timeout = null,
    ) {
        parent::__construct($message);
    }

    public static function withDevice(DeviceId $deviceId, float $timeout): self
    {
        return new self(
            sprintf('Device %s did not respond within %s seconds', $deviceId->toString(), $timeout),
            $deviceId->toString(),
            $timeout,
        );
    }

    public function translationId(): string
    {
        if (null !== $this->deviceId) {
            return 'protocol.exceptions.PR0006.device_timeout_with_id';
        }

        return 'protocol.exceptions.PR0005.device_timeout';
    }

    public function translationParameters(): array
    {
        return [
            '%device_id%' => $this->deviceId,
            '%timeout%' => $this->timeout,
        ];
    }

    public function translationDomain(): string
    {
        return 'protocol';
    }
}
