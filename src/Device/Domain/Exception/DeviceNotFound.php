<?php

namespace Marvin\Device\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;

class DeviceNotFound extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        string $code,
        public readonly ?string $id = null,
    ) {
        parent::__construct($message, $code);
    }

    public static function withId(DeviceId $id): self
    {
        return new self(
            sprintf('The device %d is not found', $id->toString()),
            'DE00005',
            $id->toString(),
        );
    }

    public function translationId(): string
    {
        if (null !== $this->id) {
            return 'device.exceptions.device_not_found_with_id';
        }

        return 'device.exceptions.device_not_found';
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
