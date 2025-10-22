<?php

namespace Marvin\Device\Infrastructure\Worker;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Marvin\Device\Application\Command\Device\UpdateDeviceState;
use Marvin\Device\Application\Service\VirtualDevice\Time\TimeServiceInterface;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\ValueObject\VirtualDeviceType;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:device:time:evaluate',
    description: 'Evaluates time trigger virtual devices'
)]
final readonly class TimeEvaluatorWorker
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private TimeServiceInterface $timeService,
        private SyncCommandBusInterface $syncCommandBus,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(SymfonyStyle $io): int
    {
        // Time Trigger Devices
        $timeTriggerDevices = $this->deviceRepository->getVirtualByType(VirtualDeviceType::TIME_TRIGGER);

        foreach ($timeTriggerDevices as $device) {
            try {
                $config = $device->getVirtualConfig();
                $timezone = $config['timezone'] ?? 'UTC';

                $currentTime = $this->timeService->getCurrentTime($timezone);

                $this->syncCommandBus->handle(new UpdateDeviceState(
                    deviceId: $device->getId()->toString(),
                    capabilityName: 'current_time',
                    value: $currentTime->format('H:i:s')
                ));

                $this->syncCommandBus->handle(new UpdateDeviceState(
                    deviceId: $device->getId()->toString(),
                    capabilityName: 'current_date',
                    value: $currentTime->format('Y-m-d')
                ));

                $this->syncCommandBus->handle(new UpdateDeviceState(
                    deviceId: $device->getId()->toString(),
                    capabilityName: 'day_of_week',
                    value: $currentTime->format('l')
                ));

                $device->markOnline();
                $this->deviceRepository->save($device);

            } catch (\Throwable $e) {
                $this->logger->error('Failed to update time trigger device', [
                    'deviceId' => $device->getId()->toString(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Sun Trigger Devices
        $sunTriggerDevices = $this->deviceRepository->getVirtualByType(VirtualDeviceType::SUN_TRIGGER);

        foreach ($sunTriggerDevices as $device) {
            try {
                $config = $device->getVirtualConfig();
                $latitude = $config['latitude'];
                $longitude = $config['longitude'];

                $currentTime = new \DateTimeImmutable();
                $sunTimes = $this->timeService->getSunTimes($latitude, $longitude, $currentTime);

                $this->syncCommandBus->handle(new UpdateDeviceState(
                    deviceId: $device->getId()->toString(),
                    capabilityName: 'sunrise_time',
                    value: $sunTimes->sunrise->format('H:i:s')
                ));

                $this->syncCommandBus->handle(new UpdateDeviceState(
                    deviceId: $device->getId()->toString(),
                    capabilityName: 'sunset_time',
                    value: $sunTimes->sunset->format('H:i:s')
                ));

                $this->syncCommandBus->handle(new UpdateDeviceState(
                    deviceId: $device->getId()->toString(),
                    capabilityName: 'is_day',
                    value: $sunTimes->isDay()
                ));

                $device->markOnline();
                $this->deviceRepository->save($device);

            } catch (\Throwable $e) {
                $this->logger->error('Failed to update sun trigger device', [
                    'deviceId' => $device->getId()->toString(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return Command::SUCCESS;
    }
}
