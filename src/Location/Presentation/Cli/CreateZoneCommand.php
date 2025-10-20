<?php

namespace Marvin\Location\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use Marvin\Location\Application\Command\Zone\CreateZone;
use Marvin\Location\Domain\ValueObject\Orientation;
use Marvin\Location\Domain\ValueObject\SurfaceArea;
use Marvin\Location\Domain\ValueObject\TargetPowerConsumption;
use Marvin\Location\Domain\ValueObject\TargetTemperature;
use Marvin\Location\Domain\ValueObject\ZoneType;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
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
        #[Argument(name: 'label')]
        string $label,
        #[Argument(name: 'type')]
        string $type,
        #[Option(name: 'parent')]
        string $parentZoneId,
        #[Option(name: 'surface')]
        ?float $surface = null,
        #[Option(name: 'orientation')]
        ?string $orientation = null,
        #[Option(name: 'target-temp')]
        ?float $targetTemperature = null,
        #[Option(name: 'taget-power-consumption')]
        ?float $targetPowerConsumption = null,
        #[Option(name: 'icon')]
        ?string $icon = null,
        #[Option(name: 'color')]
        ?string $color = null,
    ): int {

        try {
            $command = new CreateZone(
                label: new Label($label),
                type: ZoneType::from($type),
                parentZoneId: new ZoneId($parentZoneId),
                surfaceArea: null !== $surface ? new SurfaceArea($surface) : null,
                orientation: null !== $orientation ? Orientation::from($orientation) : null,
                targetTemperature: null !== $targetTemperature ? new TargetTemperature($targetTemperature) : null,
                targetPowerConsumption: null !== $targetPowerConsumption ? new TargetPowerConsumption($targetPowerConsumption) : null,
                icon: $icon,
                color: $color,
            );

            $zoneId = $this->syncCommandBus->handle($command);
            $io->success("Zone created: {$zoneId}");

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
