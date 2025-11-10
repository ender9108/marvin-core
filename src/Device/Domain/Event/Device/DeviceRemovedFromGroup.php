<?php

declare(strict_types=1);

namespace Marvin\Device\Domain\Event\Device;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

/**
 * Domain Event: A device was removed from a group
 *
 * Emitted when a device is removed from a group composite device
 * This can happen during group deletion or device deletion
 */
final readonly class DeviceRemovedFromGroup extends AbstractDomainEvent
{
    public function __construct(
        public string $groupId,
        public string $deviceId,
        public string $groupLabel,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'group_id' => $this->groupId,
            'device_id' => $this->deviceId,
            'group_label' => $this->groupLabel,
        ];
    }
}
