<?php

namespace Marvin\Domotic\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Marvin\Domotic\Domain\Model\Capability;
use Marvin\Domotic\Domain\ValueObject\Identity\CapabilityCompositionId;

final class CapabilityComposition
{
    public readonly CapabilityCompositionId $id;

    /** @var Collection<int, CapabilityAction> */
    private readonly Collection $capabilityactions;

    /** @var Collection<int, CapabilityState> */
    private readonly Collection $capabilitystates;

    public function __construct(
        private(set) Capability $capability
    ) {
        $this->id = new CapabilityCompositionId();
        $this->capabilityactions = new ArrayCollection();
        $this->capabilitystates = new ArrayCollection();
    }

    public function addCapabilityAction(CapabilityAction $capabilityaction): CapabilityComposition
    {
        if (!$this->capabilityactions->contains($capabilityaction)) {
            $this->capabilityactions->add($capabilityaction);
        }

        return $this;
    }

    public function removeCapabilityAction(CapabilityAction $capabilityaction): CapabilityComposition
    {
        if ($this->capabilityactions->contains($capabilityaction)) {
            $this->capabilityactions->removeElement($capabilityaction);
        }

        return $this;
    }

    public function addCapabilityState(CapabilityState $capabilitystate): CapabilityComposition
    {
        if (!$this->capabilitystates->contains($capabilitystate)) {
            $this->capabilitystates->add($capabilitystate);
        }
    }

    public function removeCapabilityState(CapabilityState $capabilitystate): CapabilityComposition
    {
        if ($this->capabilitystates->contains($capabilitystate)) {
            $this->capabilitystates->removeElement($capabilitystate);
        }

        return $this;
    }
}
