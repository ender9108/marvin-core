<?php

namespace Marvin\Domotic\Domain\Model;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Marvin\Domotic\Domain\ValueObject\Area;
use Marvin\Domotic\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\CreatedAt;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\UpdatedAt;

final class Zone
{
    public readonly ZoneId $id;

    /** @var Collection<int, Device> */
    public private(set) Collection $devices;

    public function __construct(
        private(set) Label $label,
        private(set) Area $area,
        private(set) ?Zone $parentZone = null,
        private(set) ?UpdatedAt $updatedAt = null,
        public readonly CreatedAt $createdAt = new CreatedAt(new DateTimeImmutable())
    ) {
        $this->id = new ZoneId();
        $this->devices = new ArrayCollection();
    }

    public function update(
        ?Label $label = null,
        ?Area $area = null,
        ?Zone $parentZone = null
    ): void {
        $this->label = $label ?? $this->label;
        $this->area = $area ?? $this->area;
        $this->parentZone = $parentZone ?? $this->parentZone;
    }

    public function remove(): void
    {
        foreach ($this->devices as $device) {
            $this->removeDevice($device);
        }
    }

    public function addDevice(Device $device): Zone
    {
        if (!$this->devices->contains($device)) {
            $this->devices->add($device);
            $device->setZone($this);
        }

        return $this;
    }

    public function removeDevice(Device $device): Zone
    {
        if ($this->devices->contains($device)) {
            $this->devices->removeElement($device);
            $device->setZone(null);
        }

        return $this;
    }
}
