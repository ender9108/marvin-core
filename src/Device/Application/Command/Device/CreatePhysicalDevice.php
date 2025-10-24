<?php

namespace Marvin\Device\Application\Command\Device;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Device\Domain\ValueObject\Capability;
use Marvin\Device\Domain\ValueObject\TechnicalName;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;

final readonly class CreatePhysicalDevice implements SyncCommandInterface
{
    public function __construct(
        public Label $label,
        public ProtocolId $protocolId,
        public TechnicalName $technicalName,
        public ?string $manufacturer = null,
        public ?string $model = null,
        public ?string $firmwareVersion = null,
        public ?ZoneId $zoneId = null,
        /** @var array<int, Capability> $capabilities */
        public array $capabilities = [],
        public ?Metadata $metadata = null
    ) {
    }
}
