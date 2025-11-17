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

namespace Marvin\Location\Application\CommandHandler\Zone;

use Marvin\Location\Application\Command\Zone\UpdateZoneMetricsFromDevice;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdateZoneMetricsFromDeviceHandler
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
    ) {
    }

    public function __invoke(UpdateZoneMetricsFromDevice $command): void
    {
        $zone = $this->zoneRepository->byDeviceId($command->deviceId);

        if ($zone === null) {
            // Device n'est pas encore assigné à une zone, on ignore
            return;
        }

        $metricsUpdated = false;

        if ($command->temperature !== null) {
            $zone->updateTemperatureFromDevice(
                deviceId: $command->deviceId,
                temperature: $command->temperature
            );
            $metricsUpdated = true;
        }

        if ($command->humidity !== null) {
            $zone->updateHumidityFromDevice(
                deviceId: $command->deviceId,
                humidity: $command->humidity
            );
            $metricsUpdated = true;
        }

        if ($command->powerWatts !== null) {
            $zone->updatePowerConsumptionFromDevice(
                deviceId: $command->deviceId,
                power: $command->powerWatts
            );
            $metricsUpdated = true;
        }

        if ($command->motionDetected !== null) {
            $zone->updateOccupancyFromDevice(
                deviceId: $command->deviceId,
                motionDetected: $command->motionDetected
            );
            $metricsUpdated = true;
        }

        if (!$metricsUpdated) {
            return;
        }


        $this->zoneRepository->save($zone);
    }
}
