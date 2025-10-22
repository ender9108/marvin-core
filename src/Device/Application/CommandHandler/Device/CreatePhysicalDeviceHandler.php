<?php

namespace Marvin\Device\Application\CommandHandler\Device;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Device\Application\Command\Device\CreatePhysicalDevice;
use Marvin\Device\Application\Service\Acl\ProtocolQueryServiceInterface;
use Marvin\Device\Domain\Exception\ProtocolNotAvailable;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Model\DeviceCapability;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\ValueObject\CapabilityType;
use Marvin\Shared\Domain\ValueObject\Description;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreatePhysicalDeviceHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private ProtocolQueryServiceInterface $protocolQuery,
        private LoggerInterface $logger
    ) {}

    public function __invoke(CreatePhysicalDevice $command): Device
    {
        $this->logger->info('Creating physical device', [
            'name' => $command->label,
            'protocolId' => $command->protocolId,
            'physicalAddress' => $command->physicalAddress,
        ]);

        $protocolId = new ProtocolId($command->protocolId);

        if (!$this->protocolQuery->protocolExists($protocolId)) {
            throw ProtocolNotAvailable::withId($command->protocolId);
        }

        if (!$this->protocolQuery->isProtocolEnabled($protocolId)) {
            throw ProtocolNotAvailable::withIsDisabled($command->protocolId);
        }

        // Créer le device physique
        $device = Device::createPhysical(
            label: new Label($command->label),
            protocolId: $protocolId,
            physicalAddress: $command->physicalAddress,
            manufacturer: $command->manufacturer,
            model: $command->model
        );

        // Ajouter les capabilities
        foreach ($command->capabilities as $capabilityData) {
            $capability = new DeviceCapability(
                label: new Label($capabilityData['label']),
                type: CapabilityType::from($capabilityData['type']),
                supportedActions: $capabilityData['actions'] ?? [],
                supportedStates: $capabilityData['states'] ?? [],
                description: isset($capabilityData['description']) ? new Description($capabilityData['description']) : null
            );
            $device->addCapability($capability);
        }

        // Assigner à une zone si spécifié
        if ($command->zoneId !== null) {
            $device->assignToZone(new ZoneId($command->zoneId));
        }

        // Mettre à jour le firmware si spécifié
        if ($command->firmwareVersion !== null) {
            $device->updateFirmware($command->firmwareVersion);
        }

        $this->deviceRepository->save($device);

        $this->logger->info('Physical device created', [
            'deviceId' => $device->id->toString(),
            'name' => $device->label->value,
        ]);

        return $device;
    }
}

