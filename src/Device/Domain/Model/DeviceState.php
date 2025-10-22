<?php

namespace Marvin\Device\Domain\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Marvin\Device\Domain\ValueObject\Identity\DeviceStateId;

class DeviceState
{
    public readonly DeviceStateId $id;

    public function __construct(
        private(set) string $capabilityName,
        private(set) mixed $value = null,
        private(set) ?string $unit = null,
        private(set) ?DateTimeInterface $updatedAt = null,
    ) {
    }

    public function updateValue(mixed $value, ?string $unit = null): void
    {
        $this->value = $value;
        $this->unit = $unit;
        $this->updatedAt = new DateTimeImmutable();
    }
}
