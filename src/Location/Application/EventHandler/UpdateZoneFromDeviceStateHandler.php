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

namespace Marvin\Location\Application\EventHandler;

use EnderLab\DddCqrsBundle\Application\Command\CommandBusInterface;
use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use Marvin\Location\Application\Command\Zone\UpdateZoneMetricsFromDevice;
use Marvin\Location\Application\Query\Zone\GetDeviceZoneId;
use Marvin\Shared\Domain\Event\Device\DeviceStateChanged;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdateZoneFromDeviceStateHandler
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private QueryBusInterface $queryBus,
    ) {
    }

    public function __invoke(DeviceStateChanged $event): void
    {
        $deviceId = DeviceId::fromString($event->deviceId);
        // Récupère la zone du device
        $zoneId = $this->queryBus->handle(new GetDeviceZoneId($deviceId));

        if (!$zoneId) {
            // Device pas assigné à une zone
            return;
        }

        // Extrait les métriques du state
        $temperature = $event->state['temperature'] ?? null;
        $humidity = $event->state['humidity'] ?? null;
        $occupancy = $event->state['occupancy'] ?? null;
        $power = $event->state['power'] ?? null;

        // Si aucune métrique pertinente, ignorer
        if ($temperature === null && $humidity === null && $occupancy === null && $power === null) {
            return;
        }

        // Dispatch la commande de mise à jour
        $this->commandBus->dispatch(new UpdateZoneMetricsFromDevice(
            deviceId: $deviceId,
            temperature: $temperature,
            humidity: $humidity,
            powerWatts: $power,
            motionDetected: $occupancy,
        ));
    }
}
