<?php

namespace Marvin\Location\Application\EventHandler;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use Marvin\Location\Application\Command\Zone\UpdateZoneMetricsFromDevice;
use Marvin\Location\Application\Query\Zone\GetDeviceZoneId;
use Marvin\Shared\Domain\Event\Device\DeviceStateChanged;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdateZoneFromDeviceStateHandler
{
    public function __construct(
        private SyncCommandBusInterface $syncCommandBus,
        private QueryBusInterface $queryBus,
    ) {
    }

    public function __invoke(DeviceStateChanged $event): void
    {
        // Récupère la zone du device
        $zoneId = $this->queryBus->handle(new GetDeviceZoneId($event->deviceId));

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
        $this->syncCommandBus->handle(new UpdateZoneMetricsFromDevice(
            zoneId: $zoneId,
            deviceId: $event->deviceId,
            temperature: $temperature,
            humidity: $humidity,
            isOccupied: $occupancy,
            powerWatts: $power,
        ));
    }
}
