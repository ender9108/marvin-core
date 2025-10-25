<?php

namespace Marvin\Device\Domain\Event\Group;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class NativeGroupCreated extends AbstractDomainEvent
{
    public function __construct(
        public string $compositeId,
        public string $protocol,
        public string $nativeGroupId,
        public string $nativeGroupFriendlyName,
        public string $parentGroupId,
        public array $deviceIds,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'composite_id' => $this->compositeId,
            'protocol' => $this->protocol,
            'native_group_id' => $this->nativeGroupId,
            'native_group_friendly_name' => $this->nativeGroupFriendlyName,
            'parent_group_id' => $this->parentGroupId,
            'device_ids' => $this->deviceIds,
        ];
    }
}
