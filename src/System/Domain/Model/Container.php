<?php

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
    private(set) ContainerId $id;

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
    ) {
        $this->id = new ContainerId();
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
