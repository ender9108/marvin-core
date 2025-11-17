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

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;
use Marvin\Device\Domain\ValueObject\Capability;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

/**
 * Command to update bridge device state
 *
 * Updates a specific capability state of a bridge device
 * (coordinator_info, network_topology, bridge_state, permit_join, etc.)
 */
final readonly class UpdateBridgeState implements CommandInterface
{
    public function __construct(
        public DeviceId $deviceId,
        public Capability $capability,
        public mixed $value,
    ) {
    }
}
