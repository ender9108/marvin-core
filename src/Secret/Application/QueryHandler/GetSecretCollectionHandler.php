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

namespace Marvin\Secret\Application\QueryHandler;

use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Marvin\Secret\Application\Query\GetSecretCollection;
use Marvin\Secret\Domain\Repository\SecretRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetSecretCollectionHandler
{
    public function __construct(
        private SecretRepositoryInterface $secretRepository,
    ) {
    }

    public function __invoke(GetSecretCollection $query): PaginatorInterface
    {
        return $this->secretRepository->collection(
            $query->filters,
            $query->orderBy,
            $query->page,
            $query->itemsPerPage,
        );
    }
}
