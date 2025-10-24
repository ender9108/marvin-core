<?php

namespace Marvin\Device\Domain\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Marvin\Device\Domain\ValueObject\Capability;
use Marvin\Device\Domain\ValueObject\CapabilityState;
use Marvin\Device\Domain\ValueObject\Identity\DeviceStateId;

class DeviceState
{
    public readonly DeviceStateId $id;

    public private(set) ?Device $device = null;

    public function __construct(
        private(set) Capability $capability,
        private(set) CapabilityState $capabilityState,
        private(set) mixed $value = null,
        private(set) ?string $unit = null,
        private(set) ?DateTimeInterface $updatedAt = null,
        public readonly ?DateTimeInterface $createdAt = new DateTimeImmutable(),
    ) {
    }

    public function setDevice(?Device $device): self
    {
        $this->device = $device;

        return $this;
    }

    public function updateValue(mixed $value, ?string $unit = null): void
    {
        $this->value = $value;
        $this->unit = $unit;
    }
}
