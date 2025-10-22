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
}
