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

namespace Marvin\System\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use Exception;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Marvin\System\Application\Query\Container\GetContainerCollection;
use Marvin\System\Domain\Model\Container;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:system:list-containers',
    description: 'List all containers',
)]
final readonly class ListContainersCommand
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Option(description: 'Filter by container type', name: 'type')]
        ?string $type = null,
        #[Option(description: 'Filter by container status', name: 'status')]
        ?string $status = null,
    ): int {
        try {
            $filters = [];
            if (null !== $type) {
                $filters['type'] = $type;
            }
            if (null !== $status) {
                $filters['status'] = $status;
            }

            /** @var array<Container> $containers */
            $containers = $this->queryBus->handle(new GetContainerCollection(filters: $filters));

            if (empty($containers)) {
                $io->info('No containers found.');
                return Command::SUCCESS;
            }

            $rows = [];
            foreach ($containers as $container) {
                $rows[] = [
                    $container->id->toString(),
                    $container->serviceLabel->value,
                    $container->type->value,
                    $container->status->value,
                    $container->containerLabel ?? 'N/A',
                    $container->image?->value ?? 'N/A',
                    $container->lastSyncedAt?->format('Y-m-d H:i:s') ?? 'Never',
                ];
            }

            $io->table(
                ['ID', 'Service', 'Type', 'Status', 'Container', 'Image', 'Last Synced'],
                $rows
            );

            $io->success(sprintf('Found %d container(s).', count($containers)));

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
