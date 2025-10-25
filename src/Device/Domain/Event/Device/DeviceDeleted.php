<?php

namespace Marvin\Device\Domain\Event\Device;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class DeviceDeleted extends AbstractDomainEvent
{
    public function __construct(
        public string $deviceId,
        public string $name
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'device_id' => $this->deviceId,
            'name' => $this->name,
        ];
    }
}
