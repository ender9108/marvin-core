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

namespace Marvin\Device\Domain\Model;

use DateTimeImmutable;
use DateTimeInterface;
use DomainException;
use EnderLab\DddCqrsBundle\Domain\Model\AggregateRoot;
use Marvin\Device\Domain\Event\PendingAction\PendingActionCompleted;
use Marvin\Device\Domain\Event\PendingAction\PendingActionCreated;
use Marvin\Device\Domain\Event\PendingAction\PendingActionFailed;
use Marvin\Device\Domain\Event\PendingAction\PendingActionTimeout;
use Marvin\Device\Domain\ValueObject\PendingActionStatus;
use Marvin\Shared\Domain\ValueObject\Identity\CorrelationId;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\PendingActionId;

/**
 * PendingAction - Aggregate Root
 *
 * Tracks asynchronous device actions awaiting completion or response.
 * Used for CORRELATION_ID and DEVICE_LOCK execution modes.
 *
 * Lifecycle:
 * 1. Created with WAITING status
 * 2. Complete() with result → COMPLETED
 * 3. Fail() with error → FAILED
 * 4. Timeout() after duration → TIMEOUT
 */
final class PendingAction extends AggregateRoot
{
    private function __construct(
        private(set) DeviceId $deviceId,
        private(set) ?CorrelationId $correlationId,
        private(set) PendingActionStatus $status,
        private(set) string $capability,
        private(set) string $action,
        private(set) array $parameters,
        private(set) ?array $result = null,
        private(set) ?string $errorMessage = null,
        private(set) ?DateTimeInterface $completedAt = null,
        private(set) DateTimeInterface $createdAt = new DateTimeImmutable(),
        private(set) int $timeoutSeconds = 5,
        private(set) PendingActionId $id = new PendingActionId(),
    ) {
    }

    /**
     * Create a new pending action with correlation ID (CORRELATION_ID mode)
     */
    public static function createWithCorrelation(
        DeviceId $deviceId,
        CorrelationId $correlationId,
        string $capability,
        string $action,
        array $parameters = [],
        int $timeoutSeconds = 5,
    ): self {
        $pendingAction = new self(
            deviceId: $deviceId,
            correlationId: $correlationId,
            status: PendingActionStatus::WAITING,
            capability: $capability,
            action: $action,
            parameters: $parameters,
            timeoutSeconds: $timeoutSeconds,
        );

        $pendingAction->recordEvent(new PendingActionCreated(
            pendingActionId: $pendingAction->id->toString(),
            deviceId: $deviceId->toString(),
            correlationId: $correlationId->toString(),
            capability: $capability,
            action: $action,
            parameters: $parameters,
        ));

        return $pendingAction;
    }

    /**
     * Create a new pending action with device lock (DEVICE_LOCK mode)
     */
    public static function createWithDeviceLock(
        DeviceId $deviceId,
        string $capability,
        string $action,
        array $parameters = [],
        int $timeoutSeconds = 5,
    ): self {
        $pendingAction = new self(
            deviceId: $deviceId,
            correlationId: null,
            status: PendingActionStatus::WAITING,
            capability: $capability,
            action: $action,
            parameters: $parameters,
            timeoutSeconds: $timeoutSeconds,
        );

        $pendingAction->recordEvent(new PendingActionCreated(
            pendingActionId: $pendingAction->id->toString(),
            deviceId: $deviceId->toString(),
            correlationId: null,
            capability: $capability,
            action: $action,
            parameters: $parameters,
        ));

        return $pendingAction;
    }

    /**
     * Mark action as completed with result
     */
    public function complete(array $result): void
    {
        if ($this->status->isTerminal()) {
            throw new DomainException(sprintf(
                'Cannot complete PendingAction %s: already in terminal status %s',
                $this->id->toString(),
                $this->status->value
            ));
        }

        $this->status = PendingActionStatus::COMPLETED;
        $this->result = $result;
        $this->completedAt = new DateTimeImmutable();

        $this->recordEvent(new PendingActionCompleted(
            pendingActionId: $this->id->toString(),
            deviceId: $this->deviceId->toString(),
            result: $result,
        ));
    }

    /**
     * Mark action as failed with error message
     */
    public function fail(string $errorMessage): void
    {
        if ($this->status->isTerminal()) {
            throw new DomainException(sprintf(
                'Cannot fail PendingAction %s: already in terminal status %s',
                $this->id->toString(),
                $this->status->value
            ));
        }

        $this->status = PendingActionStatus::FAILED;
        $this->errorMessage = $errorMessage;
        $this->completedAt = new DateTimeImmutable();

        $this->recordEvent(new PendingActionFailed(
            pendingActionId: $this->id->toString(),
            deviceId: $this->deviceId->toString(),
            errorMessage: $errorMessage,
        ));
    }

    /**
     * Mark action as timed out
     */
    public function timeout(): void
    {
        if ($this->status->isTerminal()) {
            throw new DomainException(sprintf(
                'Cannot timeout PendingAction %s: already in terminal status %s',
                $this->id->toString(),
                $this->status->value
            ));
        }

        $this->status = PendingActionStatus::TIMEOUT;
        $this->completedAt = new DateTimeImmutable();

        $this->recordEvent(new PendingActionTimeout(
            pendingActionId: $this->id->toString(),
            deviceId: $this->deviceId->toString(),
            timeoutSeconds: $this->timeoutSeconds,
        ));
    }

    /**
     * Check if action has expired (exceeded timeout)
     */
    public function hasExpired(): bool
    {
        if ($this->status->isTerminal()) {
            return false;
        }

        $createdAt = $this->createdAt instanceof DateTimeImmutable
            ? $this->createdAt
            : DateTimeImmutable::createFromInterface($this->createdAt);

        $expiresAt = $createdAt->modify(sprintf('+%d seconds', $this->timeoutSeconds));

        return new DateTimeImmutable() >= $expiresAt;
    }

    /**
     * Get timeout expiry time
     */
    public function getExpiresAt(): DateTimeInterface
    {
        $createdAt = $this->createdAt instanceof DateTimeImmutable
            ? $this->createdAt
            : DateTimeImmutable::createFromInterface($this->createdAt);

        return $createdAt->modify(sprintf('+%d seconds', $this->timeoutSeconds));
    }
}
