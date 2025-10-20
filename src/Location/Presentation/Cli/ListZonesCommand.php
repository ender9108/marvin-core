<?php

namespace Marvin\Location\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use Marvin\Location\Application\Query\Zone\GetZonesCollection;
use Marvin\Location\Domain\Model\Zone;
use Marvin\Location\Domain\ValueObject\ZoneType;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:location:list-zones',
    description: 'List all zones',
)]
final readonly class ListZonesCommand
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Option(name: 'type')]
        string $type,
        #[Option(name: 'parent')]
        string $parent,
    ): int
    {
        try {
            $query = new GetZonesCollection(
                type: ZoneType::from($type),
                parentZoneId: new ZoneId($parent),
            );

            /** @var Zone[] $zones */
            $zones = $this->queryBus->handle($query);

            if (empty($zones)) {
                $io->info('No zones found.');
                return Command::SUCCESS;
            }

            $rows = [];
            foreach ($zones as $zone) {
                $rows[] = [
                    $zone->id->toString(),
                    $zone->label->value,
                    $zone->type->value,
                    $zone->path->value,
                    $zone->currentTemperature ? round($zone->currentTemperature, 1) . '°C' : 'N/A',
                    $zone->isOccupied ? '✓' : '✗',
                    $zone->currentPowerConsumption ? round($zone->currentPowerConsumption, 0) . 'W' : 'N/A',
                    $zone->icon ?? '',
                    $zone->color->value ?? '',
                ];
            }

            $io->table(
                ['ID', 'Name', 'Type', 'Path', 'Temp', 'Occupied', 'Power', 'Icon', 'Color'],
                $rows
            );

            $io->success(sprintf('Found %d zone(s).', count($zones)));
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
