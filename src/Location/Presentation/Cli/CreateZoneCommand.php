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
use Marvin\Location\Application\Command\Zone\CreateZone;
use Marvin\Location\Domain\ValueObject\HexaColor;
use Marvin\Location\Domain\ValueObject\Humidity;
use Marvin\Location\Domain\ValueObject\Orientation;
use Marvin\Location\Domain\ValueObject\PowerConsumption;
use Marvin\Location\Domain\ValueObject\SurfaceArea;
use Marvin\Location\Domain\ValueObject\Temperature;
use Marvin\Location\Domain\ValueObject\ZoneName;
use Marvin\Location\Domain\ValueObject\ZoneType;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:location:create-zone',
    description: 'Create a new zone',
)]
final readonly class CreateZoneCommand
{
    public function __construct(
        private SyncCommandBusInterface $syncCommandBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(name: 'zone-name')]
        string $zoneName,
        #[Argument(name: 'type')]
        string $type,
        #[Option(name: 'parent')]
        ?string $parentZoneId = null,
        #[Option(name: 'surface')]
        ?float $surface = null,
        #[Option(name: 'orientation')]
        ?string $orientation = null,
        #[Option(name: 'target-temp')]
        ?float $targetTemperature = null,
        #[Option(name: 'target-power-consumption')]
        ?float $targetPowerConsumption = null,
        #[Option(name: 'target-humidity')]
        ?float $targetHumidity = null,
        #[Option(name: 'icon')]
        ?string $icon = null,
        #[Option(name: 'color')]
        ?string $color = null,
    ): int {
        try {
            $command = new CreateZone(
                zoneName: ZoneName::fromString($zoneName),
                type: ZoneType::from($type),
                parentZoneId: null !== $parentZoneId ? new ZoneId($parentZoneId) : null,
                surfaceArea: null !== $surface ? new SurfaceArea($surface) : null,
                orientation: null !== $orientation ? Orientation::from($orientation) : null,
                targetTemperature: null !== $targetTemperature ? Temperature::fromCelsius($targetTemperature) : null,
                targetHumidity: null !== $targetHumidity ? Humidity::fromPercentage($targetHumidity) : null,
                targetPowerConsumption: null !== $targetPowerConsumption ? PowerConsumption::fromWatts($targetPowerConsumption) : null,
                icon: $icon,
                color: null !== $color ? HexaColor::fromString($color) : null,
            );

            $zoneId = $this->syncCommandBus->handle($command);
            $io->success("Zone created: " . $zoneId);

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
