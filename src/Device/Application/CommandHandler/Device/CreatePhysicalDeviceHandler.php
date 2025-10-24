<?php

namespace Marvin\Device\Application\CommandHandler\Device;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Device\Application\Command\Device\CreatePhysicalDevice;
use Marvin\Device\Application\Service\Acl\ProtocolQueryServiceInterface;
use Marvin\Device\Domain\Exception\ProtocolNotAvailable;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Model\DeviceCapability;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
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
    ) {
    }

    public function __invoke(CreatePhysicalDevice $command): Device
    {
        $this->logger->info('Creating physical device', [
            'name' => $command->label,
            'protocolId' => $command->protocolId,
            'technicalName' => $command->technicalName,
        ]);

        $protocolId = new ProtocolId($command->protocolId);

        if (!$this->protocolQuery->protocolExists($protocolId)) {
            throw ProtocolNotAvailable::withId($command->protocolId);
        }

        if (!$this->protocolQuery->isProtocolEnabled($protocolId)) {
            throw ProtocolNotAvailable::withIsDisabled($command->protocolId);
        }

        $device = Device::createPhysical(
            label: new Label($command->label),
            protocolId: $protocolId,
            technicalName: $command->technicalName,
            manufacturer: $command->manufacturer,
            model: $command->model,
            firmwareVersion: $command->firmwareVersion,
        );

        /** @var DeviceCapability $capability */
        foreach ($command->capabilities as $capability) {
            $device->addCapability($capability);
        }

        if ($command->zoneId !== null) {
            $device->assignToZone(new ZoneId($command->zoneId));
        }

        $this->deviceRepository->save($device);

        $this->logger->info('Physical device created', [
            'deviceId' => $device->id->toString(),
            'name' => $device->label->value,
        ]);

        return $device;
    }
}
