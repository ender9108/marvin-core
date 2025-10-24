<?php

namespace Marvin\Device\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Marvin\Device\Domain\ValueObject\Capability;
use Marvin\Device\Domain\ValueObject\CapabilityAction;
use Marvin\Device\Domain\ValueObject\CapabilityCategory;
use Marvin\Device\Domain\ValueObject\CapabilityState;
use Marvin\Device\Domain\ValueObject\Identity\DeviceCapabilityId;
use Marvin\Shared\Domain\ValueObject\Description;
use Marvin\Shared\Domain\ValueObject\Metadata;

final class DeviceCapability
{
    public readonly DeviceCapabilityId $id;

    public private(set) ?Device $device = null;

    public private(set) ?CapabilityCategory $capabilityCategory = null;

    /** @var Collection<int, CapabilityAction> */
    public private(set) Collection $supportedActions;

    /** @var Collection<int, CapabilityState> */
    public private(set) Collection $supportedStates;

    public function __construct(
        private(set) Capability $capability,
        private(set) ?Metadata $metadata = null,
        private(set) ?Description $description = null,
    ) {
        $this->capabilityCategory = $this->capability->getCategory();
        $this->supportedActions = new ArrayCollection(
            CapabilityAction::getActionsForCapability($this->capability)
        );
        $this->supportedStates = new ArrayCollection(
            CapabilityState::getStatesForCapability($this->capability)
        );
    }

    public function setDevice(?Device $device): self
    {
        $this->device = $device;

        return $this;
    }

    public function supportsAction(CapabilityAction $action): bool
    {
        return $this->supportedActions->contains($action);
    }

    public function supportsState(CapabilityState $state): bool
    {
        return $this->supportedStates->contains($state);
    }
}
