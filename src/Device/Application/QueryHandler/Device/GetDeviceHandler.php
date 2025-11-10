<?php

declare(strict_types=1);

namespace Marvin\Device\Application\QueryHandler\Device;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Marvin\Device\Application\Query\Device\GetDevice;
use Marvin\Device\Domain\Exception\DeviceNotFoundException;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handler for GetDevice query
 */
#[AsMessageHandler]
final readonly class GetDeviceHandler implements QueryHandlerInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository
    ) {
    }

    public function __invoke(GetDevice $query): Device
    {
        $device = $this->deviceRepository->byId($query->deviceId);

        if ($device === null) {
            throw DeviceNotFoundException::withId($query->deviceId);
        }

        return $device;
    }
}
