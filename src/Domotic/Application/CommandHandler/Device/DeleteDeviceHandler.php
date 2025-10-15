<?php

namespace Marvin\Domotic\Application\CommandHandler\Device;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Domotic\Application\Command\Device\DeleteDevice;
use Marvin\Domotic\Domain\Repository\DeviceRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DeleteDeviceHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
    ) {
    }

    public function __invoke(DeleteDevice $command): void
    {
        $device = $this->deviceRepository->byId($command->id);
        $this->deviceRepository->remove($device);
    }
}
