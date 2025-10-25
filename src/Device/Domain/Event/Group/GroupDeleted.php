<?php

namespace Marvin\Device\Domain\Event\Group;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class GroupDeleted extends AbstractDomainEvent
{
    public function __construct(
        public string $groupId,
        public string $groupName,
        public int $totalDevicesFreed,
        public array $nativeGroupsDeleted, // ['zigbee' => 'groupe_salon_zigbee']
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'group_id' => $this->groupId,
            'group_name' => $this->groupName,
            'total_devices_freed' => $this->totalDevicesFreed,
            'native_groups_deleted' => $this->nativeGroupsDeleted,
        ];
    }
}
