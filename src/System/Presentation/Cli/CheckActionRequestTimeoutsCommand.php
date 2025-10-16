<?php

namespace Marvin\System\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Exception;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Marvin\System\Application\Query\ActionRequest\GetTimeoutActionRequestCollection;
use Marvin\System\Domain\Model\ActionRequest;
use Marvin\System\Domain\Repository\ActionRequestRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:system:check-timeouts',
    description: 'Check and mark timed out action requests',
)]
class CheckActionRequestTimeoutsCommand
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private ActionRequestRepositoryInterface $actionRequestRepository,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(
        SymfonyStyle $io,
        #[Option(name: 'timeout')]
        int $timeout = 10,
    ): int
    {
        try {
            $query = new GetTimeoutActionRequestCollection($timeout);
            /** @var PaginatorInterface $paginator */
            $paginator = $this->queryBus->handle($query);

            if ($paginator->count() === 0) {
                $io->success('No timed out action requests found');
                return Command::SUCCESS;
            }

            /** @var ActionRequest $actionRequest */
            foreach ($paginator->getIterator() as $actionRequest) {
                $actionRequest->markAsTimeout();
                $this->actionRequestRepository->save($actionRequest);

                $io->warning(sprintf(
                    'Marked as timeout: %s (entity: %s, action: %s)',
                    $actionRequest->correlationId->toString(),
                    $actionRequest->entityType,
                    $actionRequest->action
                ));
            }

            $io->success(sprintf('Marked %d action requests as timeout', $paginator->count()));

            return Command::SUCCESS;
        } catch (DomainException $de) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($de));

            return Command::FAILURE;
        }
    }
}
