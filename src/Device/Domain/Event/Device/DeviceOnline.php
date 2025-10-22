<?php

namespace Marvin\Device\Domain\Event\Device;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class DeviceOnline extends AbstractDomainEvent
{
    public function __construct(
        public string $deviceId,
        public string $label
    ) {
        parent::__construct();
    }
}
