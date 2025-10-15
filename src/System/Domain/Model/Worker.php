<?php

namespace Marvin\System\Domain\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;
use Marvin\System\Domain\ValueObject\Identity\WorkerId;
use Marvin\System\Domain\ValueObject\SupervisorProcess;
use Marvin\System\Domain\ValueObject\WorkerAllowedActions;
use Marvin\System\Domain\ValueObject\WorkerStatus;

final class Worker
{
    public readonly WorkerId $id;

    public function __construct(
        private(set) Label $label,
        private(set) SupervisorProcess $processName,
        private(set) string $command,
        private(set) string $type,
        private(set) WorkerAllowedActions $allowedActions,
        private(set) int $numProcs,
        private(set) int $priority,
        private(set) bool $autoStart,
        private(set) bool $autoRestart,
        private(set) ?WorkerStatus $status = null,
        private(set) ?Metadata $metadata = null,
        private(set) ?DateTimeInterface $lastSyncedAt = null,
        private(set) ?DateTimeInterface $updatedAt = null,
        private(set) DateTimeInterface $createdAt = new DateTimeImmutable(),
    ) {
        $this->id = new WorkerId();
    }
}
