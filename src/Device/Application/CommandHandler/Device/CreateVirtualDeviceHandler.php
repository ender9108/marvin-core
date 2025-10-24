<?php

namespace Marvin\Device\Application\CommandHandler\Device;

use DateTimeZone;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Marvin\Device\Application\Command\Device\CreateVirtualDevice;
use Marvin\Device\Domain\Exception\InvalidVirtualConfig;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Model\DeviceCapability;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\ValueObject\VirtualDeviceConfig;
use Marvin\Device\Domain\ValueObject\VirtualDeviceType;
use Marvin\Shared\Domain\Application;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateVirtualDeviceHandler
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(CreateVirtualDevice $command): Device
    {
        $this->logger->info('Creating virtual device', [
            'label' => $command->label,
            'virtualType' => $command->virtualDeviceType->value,
        ]);

        $this->validateVirtualConfig($command->virtualDeviceType, $command->virtualDeviceConfig);

        $device = Device::createVirtual(
            label: new Label($command->label),
            virtualDeviceType: $command->virtualDeviceType,
            virtualDeviceConfig: $command->virtualDeviceConfig
        );

        /** @var DeviceCapability $capability */
        foreach ($command->capabilities as $capability) {
            $device->addCapability($capability);
        }

        if ($command->zoneId !== null) {
            $device->assignToZone(new ZoneId($command->zoneId));
        }

        $this->deviceRepository->save($device);

        $this->logger->info('Virtual device created', [
            'deviceId' => $device->id->toString(),
            'name' => $device->label->value,
            'virtualType' => $command->virtualDeviceType->value,
        ]);

        return $device;
    }

    private function validateVirtualConfig(VirtualDeviceType $type, VirtualDeviceConfig $config): void
    {
        // Validation spécifique selon le type
        match ($type) {
            VirtualDeviceType::WEATHER => $this->validateWeatherConfig($config),
            VirtualDeviceType::TIME_TRIGGER => $this->validateTimeConfig($config),
            VirtualDeviceType::HTTP_TRIGGER => $this->validateHttpConfig($config),
            VirtualDeviceType::VARIABLE => $this->validateVariableConfig($config),
            default => null // Pas de validation spécifique
        };
    }

    private function validateWeatherConfig(VirtualDeviceConfig $config): void
    {
        Assert::true(
            $config->has('api_provider') &&
            $config->has('location')
        );
        Assert::inArray($config->get('api_provider'), Application::APP_WEATHER_PRIVIDER_AVAILABLES);
    }

    private function validateTimeConfig(VirtualDeviceConfig $config): void
    {
        Assert::true($config->has('timezone'));
        Assert::inArray($config->get('timezone'), DateTimeZone::listIdentifiers());
    }

    private function validateHttpConfig(VirtualDeviceConfig $config): void
    {
        Assert::true($config->has('url'));

        if (!filter_var($config->get('url'), FILTER_VALIDATE_URL)) {
            throw InvalidVirtualConfig::invalidValue('url', $config->get('url'));
        }

        Assert::true($config->has('method'));
        Assert::inArray($config->get('method'), ['GET', 'POST', 'PUT', 'DELETE', 'PATCH']);
    }

    private function validateVariableConfig(VirtualDeviceConfig $config): void
    {
        Assert::true($config->has('type'));
        Assert::inArray($config->get('type'), ['string', 'number', 'boolean', 'json']);
    }
}
