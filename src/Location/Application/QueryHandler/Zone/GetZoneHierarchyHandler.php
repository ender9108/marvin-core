<?php

namespace Marvin\Location\Application\QueryHandler\Zone;

use Marvin\Location\Application\Query\Zone\GetZoneHierarchy;
use Marvin\Location\Domain\Model\Zone;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetZoneHierarchyHandler
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
    ) {}

    public function __invoke(GetZoneHierarchy $query): array
    {
        if ($query->rootZoneId !== null) {
            $rootZone = $this->zoneRepository->byId($query->rootZoneId);

            return $this->buildHierarchy($rootZone);
        }

        $rootZones = $this->zoneRepository->getRootZones();
        return array_map(
            fn(Zone $zone) => $this->buildHierarchy($zone),
            $rootZones
        );
    }

    private function buildHierarchy(Zone $zone): array
    {
        $children = $this->zoneRepository->byParentZoneId($zone->id);

        return [
            'id' => $zone->id->toString(),
            'name' => $zone->label->value,
            'type' => $zone->type->value,
            'path' => $zone->path->value,
            'currentTemperature' => $zone->currentTemperature,
            'targetTemperature' => $zone->targetTemperature->value,
            'isOccupied' => $zone->isOccupied,
            'currentPowerConsumption' => $zone->currentPowerConsumption,
            'icon' => $zone->icon,
            'color' => $zone->color->value,
            'children' => array_map(
                fn(Zone $child) => $this->buildHierarchy($child),
                $children
            ),
        ];
    }
}
