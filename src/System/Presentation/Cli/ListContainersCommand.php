<?php

namespace Marvin\System\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Exception;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Marvin\System\Application\Query\Container\GetContainerCollection;
use Marvin\System\Domain\Model\Container;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:system:list-container',
    description: 'Get list of containers.',
)]
final readonly class ListContainersCommand
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
            $query = new GetContainerCollection(page: 1, itemsPerPage: 50);
            /** @var PaginatorInterface $containers */
            $containers = $this->queryBus->handle($query);
            $rows = [];

            /** @var Container $container */
            foreach ($containers->getIterator() as $container) {
                $rows[] = [
                    $container->id->toString(),
                    $container->containerLabel,
                    $container->serviceLabel,
                    $container->type->value,
                    $container->status->value,
                    $container->image->value,
                    implode(', ', $container->allowedActions->value),
                    $container->lastSyncedAt?->format('Y-m-d H:i'),
                ];
            }

            $io->table(
                ['Id', 'Container name', 'Service name', 'Type', 'Status', 'Image', 'Allowed actions', 'Synced at'],
                $rows
            );

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
