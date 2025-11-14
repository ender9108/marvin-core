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

use Marvin\Device\Application\Command\Device\DeleteDevice;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handler for DeleteDevice command
 *
 * Removes a device from the system.
 * Automatically removes the device from all groups it belongs to before deletion.
 * Records DeviceRemovedFromGroup events for each group.
 */
#[AsMessageHandler]
final readonly class DeleteDeviceHandler
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(DeleteDevice $command): void
    {
        $this->logger->info('Deleting device', [
            'deviceId' => $command->deviceId->toString(),
        ]);

        $device = $this->deviceRepository->byId($command->deviceId);

        // Check if device is part of any groups and auto-remove
        $groups = $this->deviceRepository->byChildDeviceId($command->deviceId);

        if (!empty($groups)) {
            $this->logger->info('Device is part of groups, removing from groups before deletion', [
                'deviceId' => $command->deviceId->toString(),
                'groupCount' => count($groups),
            ]);

            // Auto-remove device from all groups
            foreach ($groups as $group) {
                $group->removeChildDevice($command->deviceId);
                $this->deviceRepository->save($group);

                $this->logger->info('Device removed from group', [
                    'deviceId' => $command->deviceId->toString(),
                    'groupId' => $group->id->toString(),
                    'groupLabel' => $group->label->value,
                ]);
            }
        }

        // Remove device
        $this->deviceRepository->remove($device);

        $this->logger->info('Device deleted successfully', [
            'deviceId' => $device->id->toString(),
            'label' => $device->label->value,
        ]);
    }
}
