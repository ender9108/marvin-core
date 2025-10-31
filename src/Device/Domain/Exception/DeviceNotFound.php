<?php

namespace Marvin\Device\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

class DeviceNotFound extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $id = null,
    ) {
        parent::__construct($message);
    }

    public static function withId(DeviceId $id): self
    {
        return new self(
            sprintf('The device %d is not found', $id->toString()),
            $id->toString(),
        );
    }

    public function translationId(): string
    {
        if (null !== $this->id) {
            return 'device.exceptions.DE0002.device_not_found_with_id';
        }

        return 'device.exceptions.DE0001.device_not_found';
    }

    public function translationParameters(): array
    {
        return [
            '%id%' => $this->id
        ];
    }

    public function translationDomain(): string
    {
        return 'device';
    }
}
