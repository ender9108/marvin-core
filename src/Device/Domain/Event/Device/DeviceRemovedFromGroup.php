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

    public function toArray(): array
    {
        return [
            'group_id' => $this->groupId,
            'group_name' => $this->groupName,
            'device_id' => $this->deviceId,
            'device_name' => $this->deviceName,
            'was_in_native_group' => $this->wasInNativeGroup,
            'native_group_protocol' => $this->nativeGroupProtocol,
        ];
    }
}
