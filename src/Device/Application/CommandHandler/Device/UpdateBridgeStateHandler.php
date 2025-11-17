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

namespace Marvin\Device\Application\CommandHandler\Device;

use Marvin\Device\Application\Command\Device\UpdateBridgeState;
use Marvin\Device\Domain\Exception\DeviceNotFound;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\ValueObject\DeviceType;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handler for UpdateBridgeState command
 *
 * Updates a specific capability state of a bridge device
 */
#[AsMessageHandler]
final readonly class UpdateBridgeStateHandler
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(UpdateBridgeState $command): void
    {
        $this->logger->info('Updating bridge state', [
            'deviceId' => $command->deviceId->toString(),
            'capability' => $command->capability->value,
        ]);

        // Retrieve bridge device
        $device = $this->deviceRepository->byId($command->deviceId);

        // Verify it's a bridge device
        if ($device->deviceType !== DeviceType::BRIDGE) {
            throw new DeviceNotFound(
                sprintf('Device %s is not a bridge', $command->deviceId->toString())
            );
        }

        // Determine state name based on capability
        $stateName = match ($command->capability) {
            $command->capability::COORDINATOR_INFO => 'coordinator_info',
            $command->capability::NETWORK_TOPOLOGY => 'network_topology',
            $command->capability::BRIDGE_STATE => 'bridge_state',
            $command->capability::PERMIT_JOIN => 'permit_join',
            $command->capability::FIRMWARE_VERSION => 'firmware_version',
            $command->capability::HEALTH_CHECK => 'health',
            default => $command->capability->value,
        };

        // Update capability state
        $device->updatePartialState($stateName, $command->value);

        // Save device
        $this->deviceRepository->save($device);

        $this->logger->info('Bridge state updated successfully', [
            'deviceId' => $command->deviceId->toString(),
            'capability' => $command->capability->value,
            'stateName' => $stateName,
        ]);
    }
}
