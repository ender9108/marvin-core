<?php

namespace Marvin\System\Domain\Event;

use Marvin\Shared\Domain\ValueObject\Identity\UniqId;
use Marvin\System\Domain\ValueObject\Identity\ContainerId;

class ContainerActionCompleted
{
    public function __construct(
        public UniqId $correlationId,
        public ContainerId $containerId,
        public string $action,
        public bool $success,
        public ?string $output = null,
        public ?string $error = null,
    ) {
    }
}
