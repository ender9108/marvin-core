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

namespace Marvin\System\Domain\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;
use Marvin\System\Domain\ValueObject\Identity\WorkerId;
use Marvin\System\Domain\ValueObject\WorkerAllowedActions;
use Marvin\System\Domain\ValueObject\WorkerStatus;
use Marvin\System\Domain\ValueObject\WorkerType;

final class Worker
{
    public function __construct(
        private(set) Label $label,
        private(set) WorkerType $type,
        private(set) string $command,
        private(set) WorkerAllowedActions $allowedActions,
        private(set) ?int $numProcs = null,
        private(set) ?string $uptime = null,
        private(set) ?WorkerStatus $status = null,
        private(set) ?Metadata $metadata = null,
        private(set) ?DateTimeInterface $lastSyncedAt = null,
        private(set) DateTimeInterface $createdAt = new DateTimeImmutable(),
        private(set) WorkerId $id = new WorkerId(),
    ) {
    }
}
