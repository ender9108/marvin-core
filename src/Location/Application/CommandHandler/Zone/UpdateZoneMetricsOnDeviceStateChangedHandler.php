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

use EnderLab\DddCqrsBundle\Application\Command\CommandBusInterface;
use Marvin\Location\Application\Command\Zone\UpdateZoneMetricsFromDevice;
use Marvin\Location\Domain\ValueObject\Humidity;
use Marvin\Location\Domain\ValueObject\PowerConsumption;
use Marvin\Location\Domain\ValueObject\Temperature;
use Marvin\Shared\Domain\Event\Device\DeviceStateChanged;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdateZoneMetricsOnDeviceStateChangedHandler
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(DeviceStateChanged $event): void
    {
        // Extraire les métriques du state
        $temperature = $event->state['temperature'] ?? null;
        $humidity = $event->state['humidity'] ?? null;
        $power = $event->state['power'] ?? null;
        $motionDetected = $event->state['motion_detected'] ?? $event->state['occupancy'] ?? null;

        // Si aucune métrique pertinente, on ignore
        if ($temperature === null && $humidity === null && $power === null && $motionDetected === null) {
            return;
        }

        $this->commandBus->dispatch(new UpdateZoneMetricsFromDevice(
            deviceId: DeviceId::fromString($event->deviceId),
            temperature: null !== $temperature ? Temperature::fromCelsius($temperature) : null,
            humidity: null !== $humidity ? Humidity::fromPercentage($humidity) : null,
            powerWatts: null !== $power ? PowerConsumption::fromWatts($power) : null,
            motionDetected: $motionDetected,
        ));
    }
}
