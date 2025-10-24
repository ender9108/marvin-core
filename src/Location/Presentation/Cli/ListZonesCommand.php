<?php

namespace Marvin\Location\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Marvin\Location\Application\Query\Zone\GetZonesCollection;
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
        private QueryBusInterface $queryBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Option(name: 'type')]
        ?string $type = null,
        #[Option(name: 'parent')]
        ?string $parent = null,
        #[Option(name: 'page')]
        int $page = 1,
        #[Option(name: 'items-per-page')]
        int $itemsPerPage = 25,
    ): int {
        try {
            $query = new GetZonesCollection(
                type: null !== $type ? ZoneType::from($type) : null,
                parentZoneId: null !== $parent ? new ZoneId($parent) : null,
                page: $page,
                itemsPerPage: $itemsPerPage,
            );

            /** @var PaginatorInterface $zones */
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
