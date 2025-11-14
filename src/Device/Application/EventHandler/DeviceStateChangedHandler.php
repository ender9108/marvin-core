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
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * DeviceStateChangedHandler - Event Handler
 *
 * Handles DeviceStateChanged events from Protocol Context
 * Updates device states when MQTT/Protocol messages arrive
 */
#[AsMessageHandler]
final readonly class DeviceStateChangedHandler
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(DeviceStateChanged $event): void
    {
        $this->logger->info('Handling DeviceStateChanged event', [
            'nativeId' => $event->deviceId,
            'states' => $event->states,
        ]);

        try {
            // Find device by nativeId (physicalAddress)
            // We need to search across all protocols as we don't know which one yet
            $device = $this->findDeviceByNativeId($event->deviceId);

            if ($device === null) {
                $this->logger->warning('Device not found for nativeId', [
                    'nativeId' => $event->deviceId,
                ]);
                return;
            }

            // Update device states
            foreach ($event->states as $capabilityName => $value) {
                // Skip internal keys like capability_unit
                if (str_ends_with((string) $capabilityName, '_unit')) {
                    continue;
                }

                try {
                    // Extract unit if present
                    $unit = $event->states[$capabilityName . '_unit'] ?? null;

                    $device->updatePartialState($capabilityName, $value, $unit);

                    $this->logger->debug('Device state updated', [
                        'deviceId' => $device->id->toString(),
                        'capability' => $capabilityName,
                        'value' => $value,
                        'unit' => $unit,
                    ]);
                } catch (Throwable $e) {
                    $this->logger->error('Error updating device state for capability', [
                        'deviceId' => $device->id->toString(),
                        'capability' => $capabilityName,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Mark device as online
            $device->markOnline();

            // Persist changes
            $this->deviceRepository->save($device);

            $this->logger->info('Device states updated successfully', [
                'deviceId' => $device->id->toString(),
                'nativeId' => $event->deviceId,
                'updatedStates' => count($event->states),
            ]);
        } catch (Throwable $e) {
            $this->logger->error('Error handling DeviceStateChanged event', [
                'nativeId' => $event->deviceId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Find device by nativeId across all protocols
     */
    private function findDeviceByNativeId(string $nativeId): ?Device
    {
        $physicalAddress = PhysicalAddress::fromString($nativeId);

        // Try common protocols in order of likelihood
        $protocolsToTry = [
            Protocol::ZIGBEE,
            Protocol::MQTT,
            Protocol::NETWORK,
            Protocol::BLUETOOTH,
        ];

        foreach ($protocolsToTry as $protocol) {
            $device = $this->deviceRepository->byPhysicalAddress($physicalAddress, $protocol);

            if ($device !== null) {
                return $device;
            }
        }

        return null;
    }
}
