<?php

declare(strict_types=1);

namespace Marvin\Device\Application\Command\Device;

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;
use Marvin\Device\Domain\ValueObject\Capability;
use Marvin\Device\Domain\ValueObject\DeviceType;
use Marvin\Device\Domain\ValueObject\PhysicalAddress;
use Marvin\Device\Domain\ValueObject\Protocol;
use Marvin\Device\Domain\ValueObject\TechnicalName;
use Marvin\Shared\Domain\ValueObject\Description;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;

/**
 * Command to create a new physical device (ACTUATOR or SENSOR)
 */
final readonly class CreatePhysicalDevice implements CommandInterface
{
    /**
     * @param Capability[] $capabilities List of device capabilities
     */
    public function __construct(
        public Label $label,
        public DeviceType $deviceType,
        public Protocol $protocol,
        public ProtocolId $protocolId,
        public PhysicalAddress $physicalAddress,
        public TechnicalName $technicalName,
        public array $capabilities = [],
        public ?ZoneId $zoneId = null,
        public ?Description $description = null,
        public ?Metadata $metadata = null,
    ) {
    }
}
