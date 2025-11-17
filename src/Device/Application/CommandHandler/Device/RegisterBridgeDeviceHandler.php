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

namespace Marvin\Device\Application\CommandHandler\Device;

use Marvin\Device\Application\Command\Device\RegisterBridgeDevice;
use Marvin\Device\Application\Service\Acl\ProtocolQueryServiceInterface;
use Marvin\Device\Domain\Exception\ProtocolNotAvailable;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\ValueObject\Capability;
use Marvin\Device\Domain\ValueObject\DeviceType;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handler for RegisterBridgeDevice command
 *
 * Registers a new bridge/coordinator device and initializes its state with:
 * - Coordinator information (IEEE, type, firmware)
 * - Network topology data
 * - Bridge-specific capabilities
 */
#[AsMessageHandler]
final readonly class RegisterBridgeDeviceHandler
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private ProtocolQueryServiceInterface $protocolQuery,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(RegisterBridgeDevice $command): Device
    {
        $this->logger->info('Registering bridge device', [
            'label' => $command->label->value,
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

        // Build bridge capabilities with initial data
        $capabilities = [];

        // COORDINATOR_INFO capability
        if (!empty($command->coordinatorInfo)) {
            $capabilities[Capability::COORDINATOR_INFO->value] = [
                'stateName' => 'coordinator_info',
                'initialValue' => $command->coordinatorInfo,
            ];
        }

        // NETWORK_TOPOLOGY capability
        if (!empty($command->networkTopology)) {
            $capabilities[Capability::NETWORK_TOPOLOGY->value] = [
                'stateName' => 'network_topology',
                'initialValue' => $command->networkTopology,
            ];
        }

        // BRIDGE_STATE capability (default: online)
        $capabilities[Capability::BRIDGE_STATE->value] = [
            'stateName' => 'bridge_state',
            'initialValue' => 'online',
        ];

        // PERMIT_JOIN capability (default: disabled)
        $capabilities[Capability::PERMIT_JOIN->value] = [
            'stateName' => 'permit_join',
            'initialValue' => false,
        ];

        // HEALTH_CHECK capability (default: healthy)
        $capabilities[Capability::HEALTH_CHECK->value] = [
            'stateName' => 'health',
            'initialValue' => 'healthy',
        ];

        // Create bridge device using factory method
        $device = Device::createPhysical(
            label: $command->label,
            deviceType: DeviceType::BRIDGE,
            protocol: $command->protocol,
            protocolId: $command->protocolId,
            physicalAddress: $command->physicalAddress,
            technicalName: $command->technicalName,
            capabilities: $capabilities,
            zoneId: null, // Bridges are not assigned to zones
            description: $command->description,
            metadata: $command->metadata,
        );

        // Save device
        $this->deviceRepository->save($device);

        $this->logger->info('Bridge device registered successfully', [
            'deviceId' => $device->id->toString(),
            'label' => $device->label->value,
            'protocol' => $command->protocol->value,
        ]);

        return $device;
    }
}
