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

use Marvin\System\Domain\Event\ContainerSynced;
use Marvin\System\Domain\Repository\ContainerRepositoryInterface;
use Marvin\System\Domain\ValueObject\ContainerStatus;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(fromTransport: 'marvin_manager_response')]
final readonly class ContainerSyncedHandler
{
    public function __construct(
        private ContainerRepositoryInterface $containerRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(ContainerSynced $event): void
    {
        $this->logger->info('Container since event received', [
            'correlationId' => $event->correlationId,
        ]);

        $container = $this->containerRepository->byId($event->containerId);
        $status = ContainerStatus::from($event->status->value);
        $container->updateStatus($status);

        $this->containerRepository->save($container);
    }
}
