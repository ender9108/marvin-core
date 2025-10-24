<?php

namespace Marvin\Device\Application\CommandHandler\Device;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use EnderLab\DddCqrsBundle\Application\Event\DomainEventBusInterface;
use Marvin\Device\Application\Command\Device\RemoveDeviceFromGroup;
use Marvin\Device\Domain\Event\Device\DeviceRemovedFromGroup;
use Marvin\Device\Domain\Event\Group\NativeGroupDeleted;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Psr\Log\LoggerInterface;

final readonly class RemoveDeviceFromGroupHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private DomainEventBusInterface $eventBus,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(RemoveDeviceFromGroup $command): void
    {
        $this->logger->info('Removing device from group', [
            'groupId' => $command->groupId,
            'deviceId' => $command->deviceId,
        ]);

        // 1. Charger le groupe et le device
        $group = $this->deviceRepository->byId($command->groupId);
        $device = $this->deviceRepository->byId($command->deviceId);

        // 2. Vérifier que le device est bien dans ce groupe
        $parentComposite = $this->findDeviceParentComposite($device, $group);

        if (!$parentComposite) {
            throw DeviceNotInGroup::forDevice($device, $group);
        }

        // 3. Retirer le device du composite parent (peut être le groupe ou un native_group)
        $parentComposite->removeChild($device);
        $device->setParentId(null);

        $wasInNativeGroup = $parentComposite->isNativeGroup();
        $nativeGroupProtocol = $wasInNativeGroup
            ? $parentComposite->nativeGroupInfo?->protocolId
            : null;

        $this->logger->debug('Device removed from composite', [
            'deviceId' => $device->id->toString(),
            'compositeId' => $parentComposite->id->toString(),
            'wasInNativeGroup' => $wasInNativeGroup,
        ]);

        // 4. Si c'était un native_group et qu'il est maintenant vide, le supprimer
        if ($wasInNativeGroup
            && $command->deleteNativeGroupIfEmpty
            && empty($parentComposite->childDeviceIds)
        ) {
            $this->logger->info('Native group is now empty, deleting it', [
                'compositeId' => $parentComposite->id->toString(),
                'protocol' => $nativeGroupProtocol,
            ]);

            // Retirer le composite natif du groupe parent
            $group->removeChild($parentComposite);

            // Dispatch event pour supprimer le groupe natif dans le protocole
            $this->eventBus->dispatch(new NativeGroupDeleted(
                compositeId: $parentComposite->id->toString(),
                protocol: $nativeGroupProtocol,
                nativeGroupId: $parentComposite->nativeGroupInfo->nativeGroupId,
                nativeGroupFriendlyName: $parentComposite->nativeGroupInfo->nativeGroupName,
            ));

            // Supprimer le composite de la BDD
            $this->deviceRepository->remove($parentComposite);
        } else {
            // Sauvegarder le composite (même si vide, on le garde)
            $this->deviceRepository->save($parentComposite);
        }

        // 5. Sauvegarder les changements
        $this->deviceRepository->save($device);
        $this->deviceRepository->save($group);

        // 6. Dispatch event
        $this->eventBus->dispatch(new DeviceRemovedFromGroup(
            groupId: $group->id,
            groupName: $group->getName()->toString(),
            deviceId: $device->getId(),
            deviceName: $device->getName()->toString(),
            wasInNativeGroup: $wasInNativeGroup,
            nativeGroupProtocol: $nativeGroupProtocol,
        ));

        $this->logger->info('Device removed from group successfully', [
            'groupId' => $command->groupId,
            'deviceId' => $command->deviceId,
            'wasInNativeGroup' => $wasInNativeGroup,
        ]);
    }

    /**
     * Trouve le composite parent direct du device dans le groupe
     * (peut être le groupe lui-même ou un native_group enfant)
     */
    private function findDeviceParentComposite(Device $device, Device $group): ?Device
    {
        // Cas 1: Le device est enfant direct du groupe
        if ($group->childrenDevices->contains($device->id)) {
            return $group;
        }

        // Cas 2: Le device est dans un native_group enfant
        /** @var Device $children */
        foreach ($group->childrenDevices as $children) {
            if ($children->isNativeGroup() && $children->childrenDevices->contains($device->id)) {
                return $children;
            }
        }

        return null;
    }
}
