<?php

namespace Marvin\Device\Domain\Event\Device;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class DeviceOffline extends AbstractDomainEvent
{
    public function __construct(
        public string $deviceId,
        public string $label
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'device_id' => $this->deviceId,
            'label' => $this->label,
        ];
    }
}
