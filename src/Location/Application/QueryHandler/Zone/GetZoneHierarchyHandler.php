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

namespace Marvin\Location\Application\QueryHandler\Zone;

use Marvin\Location\Application\Query\Zone\GetZoneHierarchy;
use Marvin\Location\Domain\Model\Zone;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetZoneHierarchyHandler
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
    ) {
    }

    public function __invoke(GetZoneHierarchy $query): array
    {
        if ($query->rootZoneId !== null) {
            $rootZone = $this->zoneRepository->byId($query->rootZoneId);

            return $this->buildHierarchy($rootZone);
        }

        $rootZones = $this->zoneRepository->getRootZones();
        return array_map(
            $this->buildHierarchy(...),
            $rootZones
        );
    }

    private function buildHierarchy(Zone $zone): array
    {
        $children = $this->zoneRepository->byParentZoneId($zone->id);

        return [
            'id' => $zone->id->toString(),
            'name' => $zone->zoneName->value,
            'type' => $zone->type->value,
            'currentTemperature' => $zone->currentTemperature,
            'targetTemperature' => $zone->targetTemperature->value,
            'isOccupied' => $zone->isOccupied,
            'currentPowerConsumption' => $zone->currentPowerConsumption,
            'icon' => $zone->icon,
            'color' => $zone->color->value,
            'children' => array_map(
                $this->buildHierarchy(...),
                $children
            ),
        ];
    }
}
