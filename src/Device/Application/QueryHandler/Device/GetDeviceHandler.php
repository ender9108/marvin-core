<?php

namespace Marvin\Device\Application\QueryHandler\Device;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Marvin\Device\Application\Query\Device\GetDevice;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetDeviceHandler implements QueryHandlerInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository
    ) {}

    public function __invoke(GetDevice $query): Device
    {
        return $this->deviceRepository->byId($query->deviceId);
    }
}

