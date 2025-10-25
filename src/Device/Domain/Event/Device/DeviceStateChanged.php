<?php

namespace Marvin\Device\Domain\Event\Device;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class DeviceStateChanged extends AbstractDomainEvent
{
    public function __construct(
        public string $deviceId,
        public string $capability,
        public mixed $oldValue,
        public mixed $newValue
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'device_id' => $this->deviceId,
            'capability' => $this->capability,
            'old_value' => $this->oldValue,
            'new_value' => $this->newValue,
        ];
    }
}
