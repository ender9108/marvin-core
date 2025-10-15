<?php

namespace Marvin\System\Domain\Event;

use Marvin\Shared\Domain\ValueObject\Identity\UniqId;
use Marvin\System\Domain\ValueObject\ActionStatus;
use Marvin\System\Domain\ValueObject\Identity\ContainerId;

class ContainerSynced
{
    public function __construct(
        public UniqId $correlationId,
        public ContainerId $containerId,
        public ActionStatus $status,
        public array $details = [],
    ) {
    }
}
