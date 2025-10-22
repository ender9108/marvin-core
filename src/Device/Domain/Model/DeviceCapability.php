<?php

namespace Marvin\Device\Domain\Model;

use Marvin\Device\Domain\ValueObject\CapabilityType;
use Marvin\Shared\Domain\ValueObject\Description;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;

class DeviceCapability
{
    public function __construct(
        private(set) Label $label,
        private(set) CapabilityType $type,
        private(set) array $supportedActions = [],
        private(set) array $supportedStates = [],
        private(set) ?Metadata $metadata = null,
        private(set) ?Description $description = null,
    ) {
    }

    public function supportsAction(string $action): bool
    {
        return in_array($action, $this->supportedActions, true);
    }

    public function supportsState(string $state): bool
    {
        return in_array($state, $this->supportedStates, true);
    }
}
