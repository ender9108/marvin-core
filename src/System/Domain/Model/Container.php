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
use Marvin\System\Domain\ValueObject\ContainerAllowedActions;
use Marvin\System\Domain\ValueObject\ContainerImage;
use Marvin\System\Domain\ValueObject\ContainerStatus;
use Marvin\System\Domain\ValueObject\ContainerType;
use Marvin\System\Domain\ValueObject\Identity\ContainerId;

final class Container
{
    public function __construct(
        private(set) Label $serviceLabel,
        private(set) ContainerType $type,
        private(set) ContainerStatus $status,
        private(set) ContainerAllowedActions $allowedActions,
        private(set) ?string $containerId = null,
        private(set) ?string $containerLabel = null,
        private(set) ?ContainerImage $image = null,
        private(set) array $ports = [],
        private(set) array $volumes = [],
        private(set) ?Metadata $metadata = null,
        private(set) ?DateTimeInterface $lastSyncedAt = null,
        private(set) DateTimeInterface $createdAt = new DateTimeImmutable(),
        private(set) ContainerId $id = new ContainerId(),
    ) {
    }

    public function isActionAllowed(string $action): bool
    {
        return in_array($action, $this->allowedActions->toArray(), true);
    }

    public function updateStatus(ContainerStatus $status): void
    {
        $this->status = $status;
    }
}
