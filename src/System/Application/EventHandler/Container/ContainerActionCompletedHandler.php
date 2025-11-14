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

namespace Marvin\System\Application\EventHandler\Container;

use Marvin\System\Domain\Event\ContainerActionCompleted;
use Marvin\System\Domain\Repository\ActionRequestRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(fromTransport: 'marvin_manager_response')]
final readonly class ContainerActionCompletedHandler
{
    public function __construct(
        private ActionRequestRepositoryInterface $actionRequestRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(ContainerActionCompleted $event): void
    {
        $this->logger->info('Container action completed event received', [
            'correlationId' => $event->correlationId,
        ]);

        $actionRequest = $this->actionRequestRepository->byCorrelationId($event->correlationId);

        $actionRequest->markAsCompleted($event->success, $event->output, $event->error);
        $this->actionRequestRepository->save($actionRequest);
    }
}
