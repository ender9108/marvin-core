<?php

namespace Marvin\Device\Application\Command\Device;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Device\Domain\ValueObject\VirtualDeviceType;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;

final readonly class CreateVirtualDevice implements SyncCommandInterface
{
    public function __construct(
        public Label $label,
        public VirtualDeviceType $virtualType, // 'weather', 'time_trigger', 'http_trigger', etc.
        public array $virtualConfig, // Configuration spécifique au type virtuel
        public ?ZoneId $zoneId = null,
        public array $capabilities = [],
        public ?Metadata $metadata = null
    ) {}
}
