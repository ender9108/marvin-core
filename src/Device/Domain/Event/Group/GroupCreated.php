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
}
