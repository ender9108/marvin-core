<?php

namespace Marvin\Device\Infrastructure\Worker;

use Throwable;
use DateTimeImmutable;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Marvin\Device\Application\Command\Device\UpdateDeviceState;
use Marvin\Device\Application\Service\VirtualDevice\Time\TimeServiceInterface;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\ValueObject\Capability;
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
                    capability: Capability::CURRENT_TIME,
                    value: $currentTime->format('H:i:s')
                ));

                $this->syncCommandBus->handle(new UpdateDeviceState(
                    deviceId: $device->getId()->toString(),
                    capability: Capability::CURRENT_DATE,
                    value: $currentTime->format('Y-m-d')
                ));

                $this->syncCommandBus->handle(new UpdateDeviceState(
                    deviceId: $device->getId()->toString(),
                    capability: Capability::CURRENT_DAY_OF_WEEK,
                    value: $currentTime->format('l')
                ));

                $device->markOnline();
                $this->deviceRepository->save($device);
            } catch (Throwable $e) {
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

                $currentTime = new DateTimeImmutable();
                $sunTimes = $this->timeService->getSunTimes($latitude, $longitude, $currentTime);

                $this->syncCommandBus->handle(new UpdateDeviceState(
                    deviceId: $device->getId()->toString(),
                    capability: Capability::SUNRISE_TIME,
                    value: $sunTimes->sunrise->format('H:i:s')
                ));

                $this->syncCommandBus->handle(new UpdateDeviceState(
                    deviceId: $device->getId()->toString(),
                    capability: Capability::SUNSET_TIME,
                    value: $sunTimes->sunset->format('H:i:s')
                ));

                $this->syncCommandBus->handle(new UpdateDeviceState(
                    deviceId: $device->getId()->toString(),
                    capability: Capability::IS_DAY,
                    value: $sunTimes->isDay()
                ));

                $device->markOnline();
                $this->deviceRepository->save($device);
            } catch (Throwable $e) {
                $this->logger->error('Failed to update sun trigger device', [
                    'deviceId' => $device->getId()->toString(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return Command::SUCCESS;
    }
}
