<?php

namespace Marvin\Device\Domain\Event\Device;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class DeviceStateChanged extends AbstractDomainEvent
{
    public function __construct(
        public string $deviceId,
        public string $capabilityName,
        public mixed $oldValue,
        public mixed $newValue
    ) {
        parent::__construct();
    }
}
