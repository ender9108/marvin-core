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

namespace Marvin\Device\Application\EventHandler;

use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\ValueObject\PhysicalAddress;
use Marvin\Device\Domain\ValueObject\Protocol;
use Marvin\Shared\Domain\Event\Device\DeviceStateChanged;
use Marvin\Shared\Domain\Event\Location\ZoneDeviceAdded;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

/**
 * DeviceStateChangedHandler - Event Handler
 *
 * Handles DeviceStateChanged events from Protocol Context
 * Updates device states when MQTT/Protocol messages arrive
 */
#[AsMessageHandler]
final readonly class ZoneDeviceAddedHandler
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(ZoneDeviceAdded $event): void
    {
        $this->logger->info('Handling ZoneDeviceAdded event', [
            'deviceId' => $event->deviceId,
            'zoneId' => $event->zoneId,
        ]);

        try {


            /*$this->logger->info('Device states updated successfully', [
                'deviceId' => $device->id->toString(),
                'nativeId' => $event->deviceId,
                'updatedStates' => count($event->states),
            ]);*/
        } catch (Throwable $e) {
            /*$this->logger->error('Error handling DeviceStateChanged event', [
                'nativeId' => $event->deviceId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);*/
        }
    }
}
