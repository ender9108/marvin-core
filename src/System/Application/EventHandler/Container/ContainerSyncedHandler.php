<?php

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
