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

use Marvin\Secret\Application\Query\GetSecretsByCategory;
use Marvin\Secret\Domain\Model\Secret;
use Marvin\Secret\Domain\Repository\SecretRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetSecretsByCategoryHandler
{
    public function __construct(
        private SecretRepositoryInterface $secretRepository,
    ) {
    }

    /**
     * @return Secret[]
     */
    public function __invoke(GetSecretsByCategory $query): array
    {
        return $this->secretRepository->byCategory($query->category);
    }
}
