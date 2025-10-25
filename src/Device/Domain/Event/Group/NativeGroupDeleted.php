<?php

namespace Marvin\Device\Domain\Event\Group;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class NativeGroupDeleted extends AbstractDomainEvent
{
    public function __construct(
        public string $compositeId,
        public string $protocol,
        public string $nativeGroupId,
        public string $nativeGroupFriendlyName,
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
        ];
    }
}
