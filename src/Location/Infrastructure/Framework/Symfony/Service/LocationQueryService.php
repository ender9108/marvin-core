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

namespace Marvin\Location\Infrastructure\Framework\Symfony\Service;

use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Marvin\Shared\Application\Service\Acl\Dto\ZoneDto;
use Marvin\Shared\Application\Service\Acl\LocationQueryServiceInterface;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;

final readonly class LocationQueryService implements LocationQueryServiceInterface
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
    ) {
    }

    public function getZone(ZoneId $id): ZoneDto
    {
        $zone = $this->zoneRepository->byId($id);

        return new ZoneDto(
            $zone->id->toString(),
            $zone->zoneName->value,
            $zone->targetTemperature->value,
            $zone->targetPowerConsumption,
            $zone->targetHumidity,
            $zone->currentTemperature->value,
            $zone->currentPowerConsumption->value,
            $zone->currentHumidity->value,
            $zone->isOccupied,
            $zone->parent?->id->toString(),
            $zone->metadata ?? [],
            $zone->childrens->toArray()
        );
    }
}
