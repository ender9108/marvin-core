<?php

declare(strict_types=1);

namespace Marvin\Device\Application\CommandHandler\Device;

use Marvin\Device\Application\Command\Device\CreatePhysicalDevice;
use Marvin\Device\Application\Service\Acl\ProtocolCapabilityServiceInterface;
use Marvin\Device\Application\Service\Acl\ProtocolQueryServiceInterface;
use Marvin\Device\Domain\Exception\CapabilityNotSupported;
use Marvin\Device\Domain\Exception\ProtocolNotAvailable;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handler for CreatePhysicalDevice command
 *
 * Creates a new physical device (ACTUATOR or SENSOR) and validates:
 * - Protocol exists and is enabled
 * - Capabilities are supported by the protocol
 */
#[AsMessageHandler]
final readonly class CreatePhysicalDeviceHandler
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private ProtocolQueryServiceInterface $protocolQuery,
        private ProtocolCapabilityServiceInterface $protocolCapability,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(CreatePhysicalDevice $command): Device
    {
        $this->logger->info('Creating physical device', [
            'label' => $command->label->value,
            'deviceType' => $command->deviceType->value,
            'protocol' => $command->protocol->value,
            'protocolId' => $command->protocolId->toString(),
        ]);

        // Validate protocol exists and is enabled
        if (!$this->protocolQuery->protocolExists($command->protocolId)) {
            throw ProtocolNotAvailable::withId($command->protocolId);
        }

        if (!$this->protocolQuery->isProtocolEnabled($command->protocolId)) {
            throw ProtocolNotAvailable::withIsDisabled($command->protocolId);
        }

        // Validate capabilities are supported by the protocol
        foreach ($command->capabilities as $capability) {
            if (!$this->protocolCapability->supportsCapability($command->protocol, $capability)) {
                throw CapabilityNotSupported::byProtocol($command->protocol->value, $capability);
            }
        }

        // Create device using factory method
        $device = Device::createPhysical(
            label: $command->label,
            deviceType: $command->deviceType,
            protocol: $command->protocol,
            protocolId: $command->protocolId,
            physicalAddress: $command->physicalAddress,
            technicalName: $command->technicalName,
            capabilities: $command->capabilities,
            zoneId: $command->zoneId,
            description: $command->description,
            metadata: $command->metadata,
        );

        // Save device
        $this->deviceRepository->save($device);

        $this->logger->info('Physical device created successfully', [
            'deviceId' => $device->id->toString(),
            'label' => $device->label->value,
            'capabilitiesCount' => count($device->capabilities),
        ]);

        return $device;
    }
}
