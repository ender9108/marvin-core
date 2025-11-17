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

namespace Marvin\Security\Application\Query;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;

final readonly class GetUsersCollection implements QueryInterface
{
    public function __construct(
        /** @param array<string, string> $criteria */
        public array $criteria = [],
        /** @param array<string, string> $orderBy */
        public array $orderBy = [],
        public int $page = 1,
        public int $itemsPerPage = 20,
    ) {
    }
}
