<?php

namespace EnderLab\Zigbee2mqttBundle\Domotic\Domain\Service\Dto;

use Marvin\Domotic\Domain\List\CapabilityActionReference;
use Marvin\Domotic\Domain\List\CapabilityReference;
use Marvin\Domotic\Domain\List\CapabilityStateReference;

final class CapabilityCompositionDescriptor
{
    /**
     * @param CapabilityActionReference[] $actions
     * @param CapabilityStateReference[] $states
     */
    public function __construct(
        public readonly CapabilityReference $capability,
        public readonly array $actions,
        public readonly array $states,
    ) {}
}
