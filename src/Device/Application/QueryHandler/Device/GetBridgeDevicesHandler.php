<?php

/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

namespace Marvin\Device\Application\QueryHandler\Device;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Marvin\Device\Application\Query\Device\GetBridgeDevices;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\ValueObject\DeviceType;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handler for GetBridgeDevices query
 *
 * Returns all bridge/coordinator devices
 */
#[AsMessageHandler]
final readonly class GetBridgeDevicesHandler implements QueryHandlerInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository
    ) {
    }

    /**
     * @return array{bridges: Device[], total: int, page: int, limit: int}
     */
    public function __invoke(GetBridgeDevices $query): array
    {
        // Get all bridge devices
        $bridges = $this->deviceRepository->byType(DeviceType::BRIDGE);

        // Apply protocol filter if specified
        if ($query->protocol !== null || $query->protocolId !== null) {
            $bridges = array_filter(
                $bridges,
                fn (Device $device) => $device->protocol === $query->protocol ||
                    ($query->protocolId !== null && $device->protocolId->equals($query->protocolId))
            );
        }

        $total = count($bridges);

        // Simple in-memory pagination (should be done at DB level in production)
        $offset = ($query->page - 1) * $query->limit;
        $bridges = array_slice($bridges, $offset, $query->limit);

        return [
            'bridges' => array_values($bridges),
            'total' => $total,
            'page' => $query->page,
            'limit' => $query->limit,
        ];
    }
}
