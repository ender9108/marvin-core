<?php

declare(strict_types=1);

namespace Marvin\Device\Application\Command\Group;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Device\Domain\Model\DeviceCapability;
use Marvin\Device\Domain\ValueObject\CompositeStrategy;
use Marvin\Device\Domain\ValueObject\ExecutionStrategy;
use Marvin\Shared\Domain\ValueObject\Description;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;

/**
 * Command to create a new group (composite device)
 *
 * Groups multiple devices to control them together
 * Can use native protocol groups or emulation
 */
final readonly class CreateGroup implements SyncCommandInterface
{
    /**
     * @param DeviceId[] $childrenDeviceIds List of device IDs to include in the group
     * @param DeviceCapability[]|array $capabilities List of group capabilities
     */
    public function __construct(
        public Label $label,
        public array $childrenDeviceIds,
        public array $capabilities = [],
        public CompositeStrategy $compositeStrategy = CompositeStrategy::NATIVE_IF_AVAILABLE,
        public ExecutionStrategy $executionStrategy = ExecutionStrategy::BROADCAST,
        public ?ZoneId $zoneId = null,
        public ?Description $description = null,
        public ?Metadata $metadata = null,
    ) {
    }
}
