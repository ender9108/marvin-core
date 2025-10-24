<?php

namespace Marvin\Device\Application\Command\Device;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Device\Domain\ValueObject\VirtualDeviceConfig;
use Marvin\Device\Domain\ValueObject\VirtualDeviceType;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;

final readonly class CreateVirtualDevice implements SyncCommandInterface
{
    public function __construct(
        public Label $label,
        public VirtualDeviceType $virtualDeviceType, // 'weather', 'time_trigger', 'http_trigger', etc.
        public VirtualDeviceConfig $virtualDeviceConfig, // Configuration spécifique au type virtuel
        public ?ZoneId $zoneId = null,
        public ?Metadata $metadata = null,
        /** array<int, DeviceCapability> */
        public array $capabilities = [],
    ) {
    }
}
