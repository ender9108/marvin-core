<?php

/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

namespace Marvin\Device\Application\Command\Device;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Device\Domain\ValueObject\PhysicalAddress;
use Marvin\Device\Domain\ValueObject\Protocol;
use Marvin\Device\Domain\ValueObject\TechnicalName;
use Marvin\Shared\Domain\ValueObject\Description;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;

/**
 * Command to register a new bridge/coordinator device
 *
 * A bridge is a special device type that manages the network for a protocol
 * (e.g., Zigbee coordinator, Z-Wave controller, Thread border router)
 */
final readonly class RegisterBridgeDevice implements SyncCommandInterface
{
    /**
     * @param array<string, mixed> $coordinatorInfo Initial coordinator information (IEEE, type, firmware, etc.)
     * @param array<string, mixed> $networkTopology Initial network topology data
     */
    public function __construct(
        public Label $label,
        public Protocol $protocol,
        public ProtocolId $protocolId,
        public PhysicalAddress $physicalAddress,
        public TechnicalName $technicalName,
        public array $coordinatorInfo = [],
        public array $networkTopology = [],
        public ?Description $description = null,
        public ?Metadata $metadata = null,
    ) {
    }
}
