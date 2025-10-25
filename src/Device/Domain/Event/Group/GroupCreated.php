<?php

namespace Marvin\Device\Domain\Event\Group;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class GroupCreated extends AbstractDomainEvent
{
    public function __construct(
        public string $groupId,
        public string $name,
        public array $childDeviceIds,
        public bool $hasNativeSupport
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'group_id' => $this->groupId,
            'name' => $this->name,
            'device_ids' => $this->childDeviceIds,
            'has_native_support' => $this->hasNativeSupport,
        ];
    }
}
