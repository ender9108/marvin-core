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
use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Exception;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Marvin\System\Application\Query\Worker\GetWorkerCollection;
use Marvin\System\Domain\Model\Worker;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:system:list-worker',
    description: 'Get list of workers.',
)]
final readonly class ListWorkersCommand
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io
    ): int {
        try {
            $query = new GetWorkerCollection(page: 1, itemsPerPage: 50);
            /** @var PaginatorInterface $workers */
            $workers = $this->queryBus->handle($query);
            $rows = [];

            /** @var Worker $worker */
            foreach ($workers->getIterator() as $worker) {
                $instances = [];

                if (
                    null !== $worker->metadata &&
                    isset($worker->metadata->value['instances'])
                ) {
                    foreach ($worker->metadata->value['instances'] as $instance) {
                        $instances[] = '['.$instance['number'].'] - '.$instance['status'];
                    }
                }

                $rows[] = [
                    $worker->id->toString(),
                    $worker->label->value,
                    $worker->type->value,
                    $worker->status->value,
                    $worker->numProcs,
                    $worker->uptime,
                    implode(' | ', $instances),
                    $worker->lastSyncedAt?->format('Y-m-d H:i'),
                ];
            }

            $io->table(
                ['Id', 'Worker name', 'Type', 'Status', 'Nb procs', 'Uptime', 'Instances status', 'Synced at'],
                $rows
            );

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
