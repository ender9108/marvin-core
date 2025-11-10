<?php

declare(strict_types=1);

namespace Marvin\Device\Application\CommandHandler\PendingAction;

use Marvin\Device\Application\Command\PendingAction\FailPendingAction;
use Marvin\Device\Domain\Repository\PendingActionRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

/**
 * FailPendingActionHandler - Command Handler
 *
 * Marks a pending action as failed with error message
 */
#[AsMessageHandler]
final readonly class FailPendingActionHandler
{
    public function __construct(
        private PendingActionRepositoryInterface $pendingActionRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(FailPendingAction $command): void
    {
        $this->logger->info('Failing pending action', [
            'correlationId' => $command->correlationId->toString(),
        ]);

        try {
            $pendingAction = $this->pendingActionRepository->byCorrelationId($command->correlationId);

            if ($pendingAction === null) {
                $this->logger->warning('PendingAction not found for correlation ID', [
                    'correlationId' => $command->correlationId->toString(),
                ]);
                return;
            }

            $pendingAction->fail($command->errorMessage);
            $this->pendingActionRepository->save($pendingAction);

            $this->logger->info('PendingAction marked as FAILED', [
                'correlationId' => $command->correlationId->toString(),
                'deviceId' => $pendingAction->deviceId->toString(),
                'error' => $command->errorMessage,
            ]);
        } catch (Throwable $e) {
            $this->logger->error('Error failing PendingAction', [
                'correlationId' => $command->correlationId->toString(),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
