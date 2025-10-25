<?php

namespace Marvin\System\Domain\Event;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;
use Marvin\Shared\Domain\ValueObject\Identity\UniqId;
use Marvin\System\Domain\ValueObject\ActionStatus;
use Marvin\System\Domain\ValueObject\Identity\ContainerId;

final readonly class ContainerSynced extends AbstractDomainEvent
{
    public function __construct(
        public UniqId $correlationId,
        public ContainerId $containerId,
        public ActionStatus $status,
        public array $details = [],
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'correlation_id' => $this->correlationId->toString(),
            'container_id' => $this->containerId->toString(),
            'status' => $this->status->value,
            'details' => $this->details,
        ];
    }
}
