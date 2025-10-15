<?php

namespace Marvin\Domotic\Application\CommandHandler\Device;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Domotic\Application\Command\Device\CreateDevice;
use Marvin\Domotic\Domain\Model\Device;
use Marvin\Domotic\Domain\Repository\CapabilityCompositionRepositoryInterface;
use Marvin\Domotic\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Domotic\Domain\Repository\GroupRepositoryInterface;
use Marvin\Domotic\Domain\Repository\ProtocolRepositoryInterface;
use Marvin\Domotic\Domain\Repository\ZoneRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateDeviceHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private ZoneRepositoryInterface $zoneRepository,
        private ProtocolRepositoryInterface $protocolRepository,
        private GroupRepositoryInterface $groupRepository,
        private CapabilityCompositionRepositoryInterface $capabilityCompositionRepository,
    ) {
    }

    public function __invoke(CreateDevice $command): Device
    {
        $zone = null;
        $protocol = null;

        if (!empty($command->zone)) {
            $zone = $this->zoneRepository->byId($command->zone);
        }

        if (!empty($command->protocol)) {
            $protocol = $this->protocolRepository->byId($command->protocol);
        }

        $device = new Device(
            $command->label,
            $command->technicalName,
            $protocol,
            $zone
        );

        foreach ($command->groups as $group) {
            $device->addGroup($this->groupRepository->byId($group));
        }

        foreach ($command->capabilityCompositions as $capabilityComposition) {
            $device->addCapabilityComposition($this->capabilityCompositionRepository->byId($capabilityComposition));
        }

        $this->deviceRepository->save($device);

        return $device;
    }
}
