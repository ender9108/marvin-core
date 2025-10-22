<?php

namespace Marvin\Device\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Device\Domain\Model\Device;

final class CompositeDeviceNotAllowedInGroup extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        string $code,
        public readonly ?string $id = null,
        public readonly ?string $label = null,
        public readonly ?string $strategy = null,
    ) {
        parent::__construct($message, $code);
    }

    public static function withDevice(Device $device): self
    {
        return new self(
            sprintf(
                'Composite device "%s" (ID: %s, strategy: %s) cannot be added to a group. Maximum depth is 1 level.',
                $device->id->toString(),
                $device->label->value,
                $device->compositeStrategy?->value ?? 'unknown',
            ),
            'D00011',
            $device->id->toString(),
            $device->label->value,
            $device->compositeStrategy?->value ?? 'unknown',
        );
    }

    public function translationId(): string
    {
        return 'device.exceptions.composite_device_not_allowed_in_group';
    }

    public function translationParameters(): array
    {
        return [
            '%device_id%' => $this->id,
            '%device_name%' => $this->label,
            '%strategy%' => $this->strategy,
        ];
    }

    public function translationDomain(): string
    {
        return 'device';
    }
}
