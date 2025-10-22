<?php

namespace Marvin\Device\Application\QueryHandler\Device;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Marvin\Device\Application\Query\Device\GetDevicesByZone;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetDevicesByZoneHandler implements QueryHandlerInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository
    ) {}

    public function __invoke(GetDevicesByZone $query): array
    {
        return $this->deviceRepository->byZoneId($query->zoneId);
    }
}

