<?php

declare(strict_types=1);

namespace Marvin\Device\Domain\Event\PendingAction;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

/**
 * PendingActionCompleted - Domain Event
 *
 * Raised when a pending action completes successfully with a result
 */
final readonly class PendingActionCompleted extends AbstractDomainEvent
{
    public function __construct(
        public string $pendingActionId,
        public string $deviceId,
        public array $result,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'pending_action_id' => $this->pendingActionId,
            'device_id' => $this->deviceId,
            'result' => $this->result,
        ];
    }
}
