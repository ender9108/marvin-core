<?php

declare(strict_types=1);

namespace Marvin\Device\Application\Query\Group;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;

/**
 * Query to get all groups
 */
final readonly class GetGroupsCollection implements QueryInterface
{
    public function __construct(
        public ?ZoneId $zoneId = null,
        public int $page = 1,
        public int $limit = 50,
    ) {
    }
}
