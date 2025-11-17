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

namespace Marvin\Device\Domain\Repository;

use Marvin\Device\Domain\Model\PendingAction;
use Marvin\Device\Domain\ValueObject\PendingActionStatus;
use Marvin\Shared\Domain\ValueObject\Identity\CorrelationId;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\PendingActionId;

/**
 * PendingActionRepositoryInterface - Repository for PendingAction Aggregate
 *
 * Provides persistence operations for tracking asynchronous device actions
 */
interface PendingActionRepositoryInterface
{
    /**
     * Find a pending action by its ID
     */
    public function byId(PendingActionId $id): ?PendingAction;

    /**
     * Find a pending action by correlation ID (for CORRELATION_ID mode)
     */
    public function byCorrelationId(CorrelationId $correlationId): ?PendingAction;

    /**
     * Find the active (WAITING) pending action for a device (for DEVICE_LOCK mode)
     * Returns null if no action is pending for this device
     */
    public function findActivePendingActionForDevice(DeviceId $deviceId): ?PendingAction;

    /**
     * Find all pending actions with a specific status
     *
     * @return PendingAction[]
     */
    public function byStatus(PendingActionStatus $status): array;

    /**
     * Find all expired pending actions (still WAITING but past timeout)
     * Used by cleanup jobs to mark timed out actions
     *
     * @return PendingAction[]
     */
    public function findExpired(): array;

    /**
     * Save a pending action (create or update)
     */
    public function save(PendingAction $pendingAction): void;

    /**
     * Remove a pending action (for cleanup)
     */
    public function remove(PendingAction $pendingAction): void;

    /**
     * Check if a device currently has an active pending action (DEVICE_LOCK check)
     */
    public function hasActivePendingAction(DeviceId $deviceId): bool;
}
