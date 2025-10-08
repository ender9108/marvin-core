<?php

namespace Marvin\Domotic\Domain\Model;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Marvin\Domotic\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\CreatedAt;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\UpdatedAt;

final class Device
{
    public readonly DeviceId $id;

    /** @var Collection<int, Group>  */
    public private(set) Collection $groups;

    private Collection $capabilityCompositions;

    public function __construct(
        private(set) Label $label,
        private(set) ?string $technicalName = null,
        private(set) ?Protocol $protocol = null,
        private(set) ?Zone $zone = null,
        private(set) ?UpdatedAt $updatedAt = null,
        public readonly CreatedAt $createdAt = new CreatedAt(new DateTimeImmutable()),
    ) {
        $this->id = new DeviceId();
        $this->groups = new ArrayCollection();
        $this->capabilityCompositions = new ArrayCollection();

    }

    public function update(
        ?Label $label = null,
        ?string $technicalName = null
    ): Device
    {
        $this->label = $label ?? $this->label;
        $this->technicalName = $technicalName ?? $this->technicalName;

        return $this;
    }

    public function changeProtocol(?Protocol $protocol): self
    {
        $this->protocol = $protocol;

        return $this;
    }

    public function changeZone(?Zone $zone): self
    {
        $this->zone = $zone;

        return $this;
    }

    public function addGroup(Group $group): Device
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
            $group->addDevice($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): Device
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            $group->removeDevice($this);
        }

        return $this;
    }

    public function addCapabilityComposition(CapabilityComposition $capabilityComposition): Device
    {
        if (!$this->capabilityCompositions->contains($capabilityComposition)) {
            $this->capabilityCompositions->add($capabilityComposition);
        }

        return $this;
    }

    public function removeCapabilityComposition(CapabilityComposition $capabilityComposition): Device
    {
        if ($this->capabilityCompositions->contains($capabilityComposition)) {
            $this->capabilityCompositions->removeElement($capabilityComposition);
        }

        return $this;
    }
}
