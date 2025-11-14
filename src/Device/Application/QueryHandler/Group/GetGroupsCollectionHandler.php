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

namespace Marvin\Device\Application\QueryHandler\Group;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Marvin\Device\Application\Query\Group\GetGroupsCollection;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\ValueObject\CompositeType;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handler for GetGroupsCollection query
 */
#[AsMessageHandler]
final readonly class GetGroupsCollectionHandler implements QueryHandlerInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository
    ) {
    }

    /**
     * @return array{groups: Device[], total: int, page: int, limit: int}
     */
    public function __invoke(GetGroupsCollection $query): array
    {
        $groups = $this->deviceRepository->getCompositeDevices(CompositeType::GROUP);

        // Filter by zone if specified
        if ($query->zoneId !== null) {
            $groups = array_filter(
                $groups,
                fn ($group) => $group->zoneId?->equals($query->zoneId) ?? false
            );
        }

        $total = count($groups);

        // Simple in-memory pagination
        $offset = ($query->page - 1) * $query->limit;
        $groups = array_slice($groups, $offset, $query->limit);

        return [
            'groups' => array_values($groups),
            'total' => $total,
            'page' => $query->page,
            'limit' => $query->limit,
        ];
    }
}
