<?php

namespace Marvin\Domotic\Domain\Model;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Marvin\Domotic\Domain\ValueObject\Identity\GroupId;
use Marvin\Shared\Domain\ValueObject\CreatedAt;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Slug;
use Marvin\Shared\Domain\ValueObject\UpdatedAt;

final class Group
{
    public readonly GroupId $id;

    /** @var Collection<int, Device>  */
    public private(set) Collection $devices;

    public function __construct(
        private(set) Label $label,
        private(set) Slug $slug,
        private(set) ?UpdatedAt $updatedAt = null,
        public readonly CreatedAt $createdAt = new CreatedAt(new DateTimeImmutable())
    ) {
        $this->id = new GroupId();
        $this->devices = new ArrayCollection();
    }

    public function addDevice(Device $device): Group
    {
        if (!$this->devices->contains($device)) {
            $this->devices->add($device);
            $device->addGroup($this);
        }

        return $this;
    }

    public function removeDevice(Device $device): Group
    {
        if ($this->devices->contains($device)) {
            $this->devices->removeElement($device);
            $device->removeGroup($this);
        }

        return $this;
    }
}
