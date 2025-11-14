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

use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use Exception;
use Marvin\Location\Application\Query\Zone\GetZonesCollection;
use Marvin\Location\Domain\Model\Zone;
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
    ): int {
        try {
            /** @var array $zones */
            $zones = $this->queryBus->handle(new GetZonesCollection());

            if (empty($zones)) {
                $io->info('No zones found.');
                return Command::SUCCESS;
            }

            $rows = [];
            /** @var Zone $zone */
            foreach ($zones as $zone) {
                $rows[] = [
                    $zone->id->toString(),
                    $zone->zoneName->value,
                    $zone->type->value,
                    $zone->currentTemperature ? round($zone->currentTemperature->value, 1) . '°C' : 'N/A',
                    $zone->currentPowerConsumption ? round($zone->currentPowerConsumption->value, 0) . 'W' : 'N/A',
                    $zone->currentHumidity ? round($zone->currentHumidity->value, 0) . '%' : 'N/A',
                    $zone->isOccupied ? '✓' : '✗',
                    $zone->icon ?? '',
                    $zone->color->value ?? '',
                ];
            }

            $io->table(
                ['ID', 'Name', 'Type', 'Temp', 'Power', 'Humi', 'Occupied', 'Icon', 'Color'],
                $rows
            );

            $io->success(sprintf('Found %d zone(s).', count($zones)));
            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
