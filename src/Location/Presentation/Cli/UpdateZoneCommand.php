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

namespace Marvin\Location\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Exception;
use Marvin\Location\Application\Command\Zone\UpdateZone;
use Marvin\Location\Domain\ValueObject\HexaColor;
use Marvin\Location\Domain\ValueObject\Orientation;
use Marvin\Location\Domain\ValueObject\PowerConsumption;
use Marvin\Location\Domain\ValueObject\SurfaceArea;
use Marvin\Location\Domain\ValueObject\Temperature;
use Marvin\Location\Domain\ValueObject\ZoneName;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:location:update-zone',
    description: 'Update zone infos',
)]
final readonly class UpdateZoneCommand
{
    public function __construct(
        private SyncCommandBusInterface $syncCommandBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(name: 'zoneId')]
        string $zoneId,
        #[Option(name: 'zone-name')]
        ?string $zoneName = null,
        #[Option(name: 'surface-area')]
        ?float $surfaceArea = null,
        #[Option(name: 'orientation')]
        ?string $orientation = null,
        #[Option(name: 'target-temp')]
        ?float $targetTemperature = null,
        #[Option(name: 'target-power-consumption')]
        ?float $targetPowerConsumption = null,
        #[Option(name: 'icon')]
        ?string $icon = null,
        #[Option(name: 'color')]
        ?string $color = null,
    ): int {
        try {
            $command = new UpdateZone(
                ZoneId::fromString($zoneId),
                null !== $zoneName ? ZoneName::fromString($zoneName) : null,
                null !== $surfaceArea ? SurfaceArea::fromFloat($surfaceArea) : null,
                null !== $orientation ? Orientation::from($orientation) : null,
                null !== $targetTemperature ? Temperature::fromCelsius($targetTemperature) : null,
                null !== $targetPowerConsumption ? PowerConsumption::fromWatts($targetPowerConsumption) : null,
                $icon,
                null !== $color ? HexaColor::fromString($color) : null,
            );

            $zoneId = $this->syncCommandBus->handle($command);
            $io->success("Zone updated: ".$zoneId);

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
