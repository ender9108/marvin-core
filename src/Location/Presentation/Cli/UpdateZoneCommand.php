<?php

namespace Marvin\Location\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use Marvin\Location\Application\Command\Zone\UpdateZone;
use Marvin\Location\Domain\ValueObject\HexaColor;
use Marvin\Location\Domain\ValueObject\Orientation;
use Marvin\Location\Domain\ValueObject\SurfaceArea;
use Marvin\Location\Domain\ValueObject\TargetPowerConsumption;
use Marvin\Location\Domain\ValueObject\TargetTemperature;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
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
        #[Option(name: 'label')]
        ?string $label = null,
        #[Option(name: 'surfaceArea')]
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
                null !== $label ? Label::fromString($label) : null,
                null !== $surfaceArea ? SurfaceArea::fromFloat($surfaceArea) : null,
                null !== $orientation ? Orientation::from($orientation) : null,
                null !== $targetTemperature ? TargetTemperature::fromFloat($targetTemperature) : null,
                null !== $targetPowerConsumption ? TargetPowerConsumption::fromFloat($targetPowerConsumption) : null,
                $icon,
                null !== $color ? HexaColor::fromString($color) : null,
            );

            $zoneId = $this->syncCommandBus->handle($command);
            $io->success("Zone updated: ".$zoneId);

            return Command::SUCCESS;
        } catch (DomainException $de) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($de));

            return Command::FAILURE;
        } catch (\Exception $e) {
            $io->error("Failed: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
