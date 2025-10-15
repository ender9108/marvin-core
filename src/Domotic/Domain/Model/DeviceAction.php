<?php

namespace Marvin\Domotic\Domain\Model;

use DateTimeImmutable;
use Marvin\Domotic\Domain\Model\Device;
use Marvin\Domotic\Domain\ValueObject\Identity\DeviceActionId;
use Marvin\Shared\Domain\ValueObject\CreatedAt;
use Marvin\Shared\Domain\ValueObject\UpdatedAt;

final class DeviceAction
{
    public readonly DeviceActionId $id;

    public function __construct(
        private(set) Device $device,
        private(set) ?UpdatedAt $updatedAt = null,
        public readonly CreatedAt $createdAt = new CreatedAt(new DateTimeImmutable()),
    ) {
        $this->id = new DeviceActionId();
    }
}
