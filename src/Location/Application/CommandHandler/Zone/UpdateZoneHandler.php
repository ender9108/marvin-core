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

use Marvin\Location\Application\Command\Zone\UpdateZone;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Marvin\Location\Domain\ValueObject\ZoneName;
use Marvin\Shared\Domain\Service\SluggerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdateZoneHandler
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
        private SluggerInterface $slugger,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(UpdateZone $command): string
    {
        $zone = $this->zoneRepository->byId($command->zoneId);

        if ($command->zoneName !== null) {
            $zone->updateName(
                ZoneName::fromString($command->zoneName),
                $this->slugger
            );
        }

        $zone->updateConfiguration(
            surfaceArea: $command->surfaceArea,
            orientation: $command->orientation,
            targetTemperature: $command->targetTemperature,
            targetPowerConsumption: $command->targetPowerConsumption,
            icon: $command->icon,
            color: $command->color,
        );

        $this->zoneRepository->save($zone);
        $this->logger->info('Zone updated', ['zoneId' => $command->zoneId]);

        return $command->zoneId;
    }
}
