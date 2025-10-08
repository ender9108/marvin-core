<?php

namespace Marvin\Domotic\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Marvin\Domotic\Domain\Model\Capability;
use Marvin\Domotic\Domain\ValueObject\Identity\CapabilityCompositionId;

class CapabilityComposition
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

    public function addCapabilityAction(CapabilityAction $capabilityAction): CapabilityComposition
    {
        if (!$this->capabilityactions->contains($capabilityAction)) {
            $this->capabilityactions->add($capabilityAction);
        }

        return $this;
    }

    public function removeCapabilityAction(CapabilityAction $capabilityAction): CapabilityComposition
    {
        if ($this->capabilityactions->contains($capabilityAction)) {
            $this->capabilityactions->removeElement($capabilityAction);
        }

        return $this;
    }

    public function addCapabilityState(CapabilityState $capabilityState): CapabilityComposition
    {
        if (!$this->capabilitystates->contains($capabilityState)) {
            $this->capabilitystates->add($capabilityState);
        }
    }

    public function removeCapabilityState(CapabilityState $capabilityState): CapabilityComposition
    {
        if ($this->capabilitystates->contains($capabilityState)) {
            $this->capabilitystates->removeElement($capabilityState);
        }

        return $this;
    }
}
