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

namespace Marvin\Location\Application\QueryHandler\Zone;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Marvin\Location\Application\Query\Zone\GetZonesCollection;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetZonesCollectionHandler implements QueryHandlerInterface
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
    ) {
    }

    public function __invoke(GetZonesCollection $query): array
    {
        return $this->zoneRepository->all();
    }
}
