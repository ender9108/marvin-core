<?php

namespace Marvin\Device\Application\EventHandler;

use EnderLab\DddCqrsBundle\Application\Event\DomainEventHandlerInterface;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Protocol\Domain\Event\Protocol\ProtocolDeleted;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ProtocolDeletedHandler implements DomainEventHandlerInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(ProtocolDeleted $event): void
    {
        $this->logger->warning('Protocol deleted, marking devices as unavailable', [
            'protocolId' => $event->protocolId,
        ]);

        $protocolId = new ProtocolId($event->protocolId);
        $devices = $this->deviceRepository->byProtocolId($protocolId);

        foreach ($devices as $device) {
            $device->markUnavailable("Protocol has been deleted");
            $this->deviceRepository->save($device);
        }

        $this->logger->info('Devices marked as unavailable after protocol deletion', [
            'protocolId' => $event->protocolId,
            'affectedDevices' => count($devices),
        ]);
    }
}
