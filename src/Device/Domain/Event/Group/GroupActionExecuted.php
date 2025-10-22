<?php

namespace Marvin\Device\Domain\Event\Group;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class GroupActionExecuted extends AbstractDomainEvent
{
    public function __construct(
        public string $groupId,
        public string $action,
        public bool $usedNative,
        public int $successCount,
        public int $failureCount
    ) {
        parent::__construct();
    }
}
