<?php

namespace Marvin\System\Domain\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Marvin\Shared\Domain\ValueObject\Identity\CorrelationId;
use Marvin\Shared\Domain\ValueObject\Identity\UniqId;
use Marvin\System\Domain\ValueObject\ActionStatus;
use Marvin\System\Domain\ValueObject\Identity\ActionRequestId;

final class ActionRequest
{
    public function __construct(
        private(set) CorrelationId $correlationId,
        private(set) string $entityType,
        private(set) string $entityId,
        private(set) string $action,
        private(set) ActionStatus $status,
        private(set) array $input = [],
        private(set) ?string $output = null,
        private(set) ?string $error = null,
        private(set) ?DateTimeInterface $completedAt = null,
        private(set) DateTimeInterface $createdAt = new DateTimeImmutable(),
        private(set) ActionRequestId $id = new ActionRequestId(),
    ) {
    }

    public function markAsCompleted(bool $success, ?string $output = null, ?string $error = null): void
    {
        $this->status = $success ? ActionStatus::COMPLETED : ActionStatus::FAILED;
        $this->output = $output;
        $this->error = $error;
        $this->completedAt = new DateTimeImmutable();
    }

    public function markAsTimeout(): void
    {
        $this->status = ActionStatus::TIMEOUT;
        $this->error = 'Action timed out';
        $this->completedAt = new DateTimeImmutable();
    }
}
