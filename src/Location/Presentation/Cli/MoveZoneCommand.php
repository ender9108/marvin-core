<?php

namespace Marvin\Location\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use Marvin\Location\Application\Command\Zone\CreateZone;
use Marvin\Location\Application\Command\Zone\MoveZone;
use Marvin\Location\Domain\ValueObject\HexaColor;
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
    name: 'marvin:location:move-zone',
    description: 'Move zone to a new parent zone',
)]
final readonly class MoveZoneCommand
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
        #[Argument(name: 'newParentZoneId')]
        string $newParentZoneId,
    ): int {
        try {
            $command = new MoveZone(
                ZoneId::fromString($zoneId),
                ZoneId::fromString($newParentZoneId),
            );

            $zoneId = $this->syncCommandBus->handle($command);
            $io->success("Zone moved: ".$zoneId." => ".$newParentZoneId);

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
