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
use Marvin\System\Application\Query\Worker\GetWorkerCollection;
use Marvin\System\Domain\Model\Worker;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:system:list-workers',
    description: 'List all workers',
)]
final readonly class ListWorkersCommand
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Option(description: 'Filter by worker type', name: 'type')]
        ?string $type = null,
        #[Option(description: 'Filter by worker status', name: 'status')]
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

            /** @var array<Worker> $workers */
            $workers = $this->queryBus->handle(new GetWorkerCollection(filters: $filters));

            if (empty($workers)) {
                $io->info('No workers found.');
                return Command::SUCCESS;
            }

            $rows = [];
            foreach ($workers as $worker) {
                $rows[] = [
                    $worker->id->toString(),
                    $worker->label->value,
                    $worker->type->value,
                    $worker->status?->value ?? 'N/A',
                    $worker->numProcs ?? 'N/A',
                    $worker->uptime ?? 'N/A',
                    $worker->command,
                    $worker->lastSyncedAt?->format('Y-m-d H:i:s') ?? 'Never',
                ];
            }

            $io->table(
                ['ID', 'Label', 'Type', 'Status', 'Procs', 'Uptime', 'Command', 'Last Synced'],
                $rows
            );

            $io->success(sprintf('Found %d worker(s).', count($workers)));

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
