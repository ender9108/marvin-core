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

namespace Marvin\Device\Application\QueryHandler\Scene;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Marvin\Device\Application\Query\Scene\GetScenesCollection;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\ValueObject\CompositeType;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handler for GetScenesCollection query
 */
#[AsMessageHandler]
final readonly class GetScenesCollectionHandler implements QueryHandlerInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository
    ) {
    }

    /**
     * @return array{scenes: Device[], total: int, page: int, limit: int}
     */
    public function __invoke(GetScenesCollection $query): array
    {
        $scenes = $this->deviceRepository->getCompositeDevices(CompositeType::SCENE);

        // Filter by zone if specified
        if ($query->zoneId !== null) {
            $scenes = array_filter(
                $scenes,
                fn ($scene) => $scene->zoneId?->equals($query->zoneId) ?? false
            );
        }

        $total = count($scenes);

        // Simple in-memory pagination
        $offset = ($query->page - 1) * $query->limit;
        $scenes = array_slice($scenes, $offset, $query->limit);

        return [
            'scenes' => array_values($scenes),
            'total' => $total,
            'page' => $query->page,
            'limit' => $query->limit,
        ];
    }
}
