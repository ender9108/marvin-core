<?php
namespace Marvin\Security\Application\Query;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;

final readonly class GetUsersCollection implements QueryInterface
{
    public function __construct(
        public array $criteria = [],
        public array $orderBy = [],
        public int $page = 1,
        public int $itemsPerPage = 20,
    ) {
    }
}
