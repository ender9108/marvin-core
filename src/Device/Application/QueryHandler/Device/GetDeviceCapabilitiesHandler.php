<?php

namespace Marvin\Device\Application\QueryHandler\Device;

use Doctrine\Common\Collections\Collection;
use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Marvin\Device\Application\Query\Device\GetDeviceCapabilities;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetDeviceCapabilitiesHandler implements QueryHandlerInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository
    ) {}

    public function __invoke(GetDeviceCapabilities $query): array
    {
        $device = $this->deviceRepository->byId($query->deviceId);

        return $device->capabilities->toArray();
    }
}

