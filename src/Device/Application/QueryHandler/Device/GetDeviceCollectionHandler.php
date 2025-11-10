<?php

declare(strict_types=1);

namespace Marvin\Device\Application\QueryHandler\Device;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Marvin\Device\Application\Query\Device\GetDeviceCollection;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handler for GetDeviceCollection query
 *
 * Returns filtered list of devices
 */
#[AsMessageHandler]
final readonly class GetDeviceCollectionHandler implements QueryHandlerInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository
    ) {
    }

    /**
     * @return array{devices: Device[], total: int, page: int, limit: int}
     */
    public function __invoke(GetDeviceCollection $query): array
    {
        // Start with all devices
        $devices = $this->deviceRepository->all();

        // Apply filters
        if ($query->protocol !== null) {
            $devices = $this->deviceRepository->byProtocol($query->protocol, $query->protocolId);
        }

        if ($query->zoneId !== null) {
            $devices = $this->deviceRepository->byZone($query->zoneId);
        }

        if ($query->deviceType !== null) {
            $devices = $this->deviceRepository->byType($query->deviceType);
        }

        if ($query->capability !== null) {
            $devices = $this->deviceRepository->byCapability($query->capability);
        }

        // TODO: Apply status filter (needs to be combined with other filters)
        // TODO: Implement proper pagination at repository level

        $total = count($devices);

        // Simple in-memory pagination (should be done at DB level in production)
        $offset = ($query->page - 1) * $query->limit;
        $devices = array_slice($devices, $offset, $query->limit);

        return [
            'devices' => array_values($devices),
            'total' => $total,
            'page' => $query->page,
            'limit' => $query->limit,
        ];
    }
}
