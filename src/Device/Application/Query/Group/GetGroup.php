<?php

declare(strict_types=1);

namespace Marvin\Device\Application\Query\Group;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

/**
 * Query to get a single group by ID
 */
final readonly class GetGroup implements QueryInterface
{
    public function __construct(
        public DeviceId $groupId
    ) {
    }
}
