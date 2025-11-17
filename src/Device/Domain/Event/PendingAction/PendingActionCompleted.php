<?php

/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */
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
