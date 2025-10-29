<?php

namespace Marvin\Shared\Domain\Event\Device;

use DateTimeImmutable;
use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;

final readonly class DeviceStateChanged extends AbstractDomainEvent
{
    public function __construct(
        public string $deviceId,
        public array $states,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'device_id' => $this->deviceId,
            'states' => $this->states,
        ];
    }
}
