<?php

namespace Marvin\Device\Application\CommandHandler\Group;

use DateTimeImmutable;
use DateTimeInterface;
use Marvin\Device\Application\Command\Group\CreateGroup;
use Marvin\Device\Domain\Exception\CompositeDeviceNotAllowedInGroup;
use Marvin\Device\Domain\Exception\DeviceAlreadyInGroup;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\Service\ProtocolGroupingServiceInterface;
use Marvin\Device\Domain\ValueObject\CompositeStrategy;
use Marvin\Device\Domain\ValueObject\DeviceStatus;
use Marvin\Device\Domain\ValueObject\DeviceType;
use Marvin\Device\Domain\ValueObject\NativeGroupInfo;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateGroupHandler
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private ProtocolGroupingServiceInterface $protocolGroupingService,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(CreateGroup $command): void
    {
        $this->logger->info('Creating group with automatic protocol grouping', [
            'groupName' => $command->groupName,
            'deviceCount' => count($command->deviceIds),
        ]);

        // 1. Charger et valider les devices
        $devices = $this->loadAndValidateDevices($command->deviceIds);

        // 2. Analyser les devices pour déterminer le regroupement
        $grouping = $this->protocolGroupingService->analyzeDevicesForGrouping($devices);

        $this->logger->debug('Protocol grouping analysis', [
            'native_groups' => array_map(count(...), $grouping['native_groups']),
            'individual_devices' => count($grouping['individual_devices']),
        ]);

        // 3. Créer le groupe parent
        $parentGroup = new Device(
            label: Label::fromString($command->groupName),
            type: DeviceType::COMPOSITE,
            status: DeviceStatus::UNAVAILABLE,
            zoneId: $command->zoneId,
            parentId: null,
            isComposite: true,
            compositeStrategy: CompositeStrategy::GROUP,
            metadata: Metadata::fromArray(array_merge(
                $command->metadata ?? [],
                [
                    'type' => 'group',
                    'auto_grouped' => true,
                    'created_at' => new DateTimeImmutable()->format(DateTimeInterface::ATOM),
                ]
            )),
        );

        // 4. Créer les sous-composites natifs pour chaque protocole
        foreach ($grouping['native_groups'] as $protocol => $protocolDevices) {
            $nativeComposite = $this->createNativeGroupComposite(
                protocol: $protocol,
                devices: $protocolDevices,
                parentGroupId: $groupId,
                parentGroupName: $command->groupName,
                zoneId: $command->zoneId,
            );

            $parentGroup->addChild($nativeComposite);
            $this->deviceRepository->save($nativeComposite);

            $this->logger->info('Created native group composite', [
                'protocol' => $protocol,
                'compositeId' => $nativeComposite->id->toString(),
                'deviceCount' => count($protocolDevices),
            ]);
        }

        // 5. Ajouter les devices individuels
        foreach ($grouping['individual_devices'] as $device) {
            $parentGroup->addChild($device);

            $this->logger->debug('Added individual device to group', [
                'deviceId' => $device->getId()->toString(),
                'deviceName' => $device->getName()->toString(),
            ]);
        }

        // 6. Sauvegarder le groupe parent
        $this->deviceRepository->save($parentGroup);

        $this->logger->info('Group created successfully', [
            'groupId' => $groupId->toString(),
            'groupName' => $command->groupName,
            'nativeGroups' => count($grouping['native_groups']),
            'individualDevices' => count($grouping['individual_devices']),
            'totalDevices' => count($command->deviceIds),
        ]);

        // TODO: Dispatch event GroupCreated
        // TODO: Dispatch commands vers Protocol pour créer les groupes natifs
    }

    /**
     * Charge et valide les devices
     *
     * @param string[] $deviceIds
     * @return Device[]
     */
    private function loadAndValidateDevices(array $deviceIds): array
    {
        $devices = [];
        $devicesInGroups = [];

        /** @var DeviceId $deviceId */
        foreach ($deviceIds as $deviceId) {
            $device = $this->deviceRepository->byId($deviceId);

            // Validation 1 : Interdire les composites (profondeur max 1)
            if ($device->isComposite()) {
                throw CompositeDeviceNotAllowedInGroup::withDevice($device);
            }

            // Validation 2 : Vérifier si déjà dans un groupe
            if ($device->parentId instanceof DeviceId) {
                $existingGroup = $this->deviceRepository->find($device->parentId->toString());

                if ($existingGroup && $existingGroup->isComposite()) {
                    $devicesInGroups[] = [
                        'device' => $device,
                        'group' => $existingGroup,
                    ];
                }
            }

            $devices[] = $device;
        }

        // Si des devices sont déjà dans des groupes, bloquer
        if (!empty($devicesInGroups)) {
            throw DeviceAlreadyInGroup::withDevices(
                array_column($devicesInGroups, 'device'),
                array_column($devicesInGroups, 'group'),
            );
        }

        return $devices;
    }

    /**
     * Crée un composite natif pour un protocole
     *
     * @param Device[] $devices
     */
    private function createNativeGroupComposite(
        string $protocol,
        array $devices,
        DeviceId $parentGroupId,
        string $parentGroupName,
        ?ZoneId $zoneId = null,
    ): Device {
        $nativeGroupId = $this->protocolGroupingService->generateNativeGroupId($protocol);
        $nativeGroupFriendlyName = $this->protocolGroupingService->generateNativeGroupName(
            $protocol,
            $parentGroupName
        );

        $composite = new Device(
            label: Label::fromString(ucfirst($protocol) . " - {$parentGroupName}"),
            type: DeviceType::COMPOSITE,
            status: DeviceStatus::UNAVAILABLE,
            zoneId: $zoneId,
            parentId: $parentGroupId,
            compositeStrategy: CompositeStrategy::NATIVE_GROUP,
            nativeGroupInfo: NativeGroupInfo::create(
                protocol: $protocol,
                groupId: $nativeGroupId,
                friendlyName: $nativeGroupFriendlyName,
                metadata: [
                    'auto_created' => true,
                    'parent_group' => $parentGroupName,
                ],
            ),
            metadata: Metadata::fromArray([
                'protocol' => $protocol,
                'native_group_id' => $nativeGroupId,
                'native_group_friendly_name' => $nativeGroupFriendlyName,
            ]),
        );

        // Attacher les devices au composite
        foreach ($devices as $device) {
            $composite->addChild($device);
        }

        return $composite;
    }
}
