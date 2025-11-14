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

namespace Marvin\Device\Application\CommandHandler\PendingAction;

use Marvin\Device\Application\Command\PendingAction\CompletePendingAction;
use Marvin\Device\Domain\Repository\PendingActionRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

/**
 * CompletePendingActionHandler - Command Handler
 *
 * Marks a pending action as completed with result
 */
#[AsMessageHandler]
final readonly class CompletePendingActionHandler
{
    public function __construct(
        private PendingActionRepositoryInterface $pendingActionRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(CompletePendingAction $command): void
    {
        $this->logger->info('Completing pending action', [
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

            $pendingAction->complete($command->result);
            $this->pendingActionRepository->save($pendingAction);

            $this->logger->info('PendingAction marked as COMPLETED', [
                'correlationId' => $command->correlationId->toString(),
                'deviceId' => $pendingAction->deviceId->toString(),
            ]);
        } catch (Throwable $e) {
            $this->logger->error('Error completing PendingAction', [
                'correlationId' => $command->correlationId->toString(),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
