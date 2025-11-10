<?php

declare(strict_types=1);

namespace Marvin\Device\Domain\Event\PendingAction;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

/**
 * PendingActionCreated - Domain Event
 *
 * Raised when a new pending action is created (action sent, awaiting response)
 */
final readonly class PendingActionCreated extends AbstractDomainEvent
{
    public function __construct(
        public string $pendingActionId,
        public string $deviceId,
        public ?string $correlationId,
        public string $capability,
        public string $action,
        public array $parameters,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'pending_action_id' => $this->pendingActionId,
            'device_id' => $this->deviceId,
            'correlation_id' => $this->correlationId,
            'capability' => $this->capability,
            'action' => $this->action,
            'parameters' => $this->parameters,
        ];
    }
}
