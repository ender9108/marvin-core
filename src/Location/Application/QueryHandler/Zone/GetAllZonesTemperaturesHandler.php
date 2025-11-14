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
