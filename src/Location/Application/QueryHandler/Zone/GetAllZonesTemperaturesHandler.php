<?php

namespace Marvin\Location\Application\QueryHandler\Zone;

use Marvin\Location\Application\Query\Zone\GetAllZonesTemperatures;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetAllZonesTemperaturesHandler
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
    ) {
    }

    /**
     * @return array<string, float> [zoneId => averageTemperature]
     */
    public function __invoke(GetAllZonesTemperatures $query): array
    {
        $zones = $this->zoneRepository->all();

        $temperatures = [];
        foreach ($zones as $zone) {
            $temp = $zone->getAverageTemperature();
            if ($temp !== null) {
                $temperatures[$zone->getId()->toString()] = $temp;
            }
        }

        return $temperatures;
    }
}
