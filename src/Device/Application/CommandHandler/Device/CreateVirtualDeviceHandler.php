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
use Marvin\Device\Domain\ValueObject\CapabilityType;
use Marvin\Device\Domain\ValueObject\VirtualDeviceType;
use Marvin\Shared\Domain\ValueObject\Description;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateVirtualDeviceHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private LoggerInterface $logger
    ) {}

    public function __invoke(CreateVirtualDevice $command): Device
    {
        $this->logger->info('Creating virtual device', [
            'label' => $command->label,
            'virtualType' => $command->virtualType,
        ]);

        $virtualType = $command->virtualType;

        // Valider la configuration selon le type
        $this->validateVirtualConfig($virtualType, $command->virtualConfig);

        // Créer le device virtuel
        $device = Device::createVirtual(
            label: new Label($command->label),
            virtualType: $virtualType,
            virtualConfig: $command->virtualConfig
        );

        // Ajouter les capabilities
        foreach ($command->capabilities as $capabilityData) {
            $capability = new DeviceCapability(
                label: new Label($capabilityData['label']),
                type: CapabilityType::from($capabilityData['type']),
                supportedActions: $capabilityData['actions'] ?? [],
                supportedStates: $capabilityData['states'] ?? [],
                description: isset($capabilityData['description'])
                    ? new Description($capabilityData['description'])
                    : null
            );
            $device->addCapability($capability);
        }

        // Assigner à une zone si spécifié
        if ($command->zoneId !== null) {
            $device->assignToZone(new ZoneId($command->zoneId));
        }

        $this->deviceRepository->save($device);

        $this->logger->info('Virtual device created', [
            'deviceId' => $device->id->toString(),
            'name' => $device->label->value,
            'virtualType' => $virtualType->value,
        ]);

        return $device;
    }

    private function validateVirtualConfig(VirtualDeviceType $type, array $config): void
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

    private function validateWeatherConfig(array $config): void
    {
        Assert::keyExists($config, 'api_provider');
        Assert::keyExists($config, 'location');
        Assert::inArray($config['api_provider'], ['openweathermap', 'weatherapi', 'meteofrance']);
    }

    private function validateTimeConfig(array $config): void
    {
        Assert::keyExists($config, 'timezone');
        Assert::inArray($config['timezone'], DateTimeZone::listIdentifiers());
    }

    private function validateHttpConfig(array $config): void
    {
        Assert::keyExists($config, 'url');

        if (!filter_var($config['url'], FILTER_VALIDATE_URL)) {
            throw InvalidVirtualConfig::invalidValue('url', $config['url']);
        }

        Assert::keyExists($config, 'method');
        Assert::inArray($config['method'], ['GET', 'POST', 'PUT', 'DELETE', 'PATCH']);
    }

    private function validateVariableConfig(array $config): void
    {
        Assert::keyExists($config, 'type');
        Assert::inArray($config['type'], ['string', 'number', 'boolean', 'json']);
    }
}
