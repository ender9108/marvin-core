<?php

namespace Marvin\System\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Exception;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Marvin\System\Application\Query\Container\GetContainerCollection;
use Marvin\System\Application\Query\Worker\GetWorkerCollection;
use Marvin\System\Domain\Model\Container;
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

    /**
     * @throws Exception
     */
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
        } catch (DomainException $de) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($de));

            return Command::FAILURE;
        }
    }
}
