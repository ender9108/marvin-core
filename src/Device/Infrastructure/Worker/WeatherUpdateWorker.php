<?php

namespace Marvin\Device\Infrastructure\Worker;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Marvin\Device\Application\Command\Device\UpdateDeviceState;
use Marvin\Device\Application\Service\VirtualDevice\Weather\WeatherServiceInterface;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\ValueObject\VirtualDeviceType;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:device:weather:update',
    description: 'Updates weather virtual devices'
)]
final readonly class WeatherUpdateWorker
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private WeatherServiceInterface $weatherService,
        private SyncCommandBusInterface $syncCommandBus,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(SymfonyStyle $io): int
    {
        $this->logger->info('Starting weather update worker');

        $weatherDevices = $this->deviceRepository->getVirtualByType(VirtualDeviceType::WEATHER);

        $io->writeln(sprintf('Found %d weather devices to update', count($weatherDevices)));

        foreach ($weatherDevices as $device) {
            try {
                $config = $device->getVirtualConfig();

                $weatherData = $this->weatherService->getCurrentWeather(
                    location: $config['location'],
                    apiProvider: $config['api_provider'],
                    apiKey: $config['api_key']
                );

                // Mettre à jour les états du device
                $this->syncCommandBus->handle(new UpdateDeviceState(
                    deviceId: $device->getId()->toString(),
                    capabilityName: 'temperature',
                    value: $weatherData->temperature,
                    unit: '°C'
                ));

                $this->syncCommandBus->handle(new UpdateDeviceState(
                    deviceId: $device->getId()->toString(),
                    capabilityName: 'humidity',
                    value: $weatherData->humidity,
                    unit: '%'
                ));

                $this->syncCommandBus->handle(new UpdateDeviceState(
                    deviceId: $device->getId()->toString(),
                    capabilityName: 'pressure',
                    value: $weatherData->pressure,
                    unit: 'hPa'
                ));

                $this->syncCommandBus->handle(new UpdateDeviceState(
                    deviceId: $device->getId()->toString(),
                    capabilityName: 'condition',
                    value: $weatherData->condition
                ));

                $device->markOnline();
                $this->deviceRepository->save($device);

                $io->success(sprintf(
                    '✓ Updated weather device: %s (temp: %.1f°C)',
                    $device->getName()->toString(),
                    $weatherData->temperature
                ));

            } catch (\Throwable $e) {
                $this->logger->error('Failed to update weather device', [
                    'deviceId' => $device->getId()->toString(),
                    'error' => $e->getMessage(),
                ]);

                $device->markOffline();
                $this->deviceRepository->save($device);

                $io->error(sprintf(
                    '✗ Failed to update weather device: %s - %s',
                    $device->getName()->toString(),
                    $e->getMessage()
                ));
            }
        }

        $this->logger->info('Weather update worker completed');

        return Command::SUCCESS;
    }
}
