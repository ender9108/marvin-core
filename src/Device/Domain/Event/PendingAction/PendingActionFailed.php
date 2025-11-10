<?php

declare(strict_types=1);

namespace Marvin\Device\Domain\Event\PendingAction;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

/**
 * PendingActionFailed - Domain Event
 *
 * Raised when a pending action fails with an error
 */
final readonly class PendingActionFailed extends AbstractDomainEvent
{
    public function __construct(
        public string $pendingActionId,
        public string $deviceId,
        public string $errorMessage,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'pending_action_id' => $this->pendingActionId,
            'device_id' => $this->deviceId,
            'error_message' => $this->errorMessage,
        ];
    }
}
