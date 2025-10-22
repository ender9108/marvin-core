<?php

namespace Marvin\Device\Domain\Event\Device;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class DeviceRemovedFromGroup extends AbstractDomainEvent
{
    public function __construct(
        public string $groupId,
        public string $groupName,
        public string $deviceId,
        public string $deviceName,
        public bool $wasInNativeGroup,
        public ?string $nativeGroupProtocol = null,
    ) {
        parent::__construct();
    }
}
