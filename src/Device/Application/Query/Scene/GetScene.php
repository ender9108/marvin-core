<?php

declare(strict_types=1);

namespace Marvin\Device\Application\Query\Scene;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

/**
 * Query to get a single scene by ID
 */
final readonly class GetScene implements QueryInterface
{
    public function __construct(
        public DeviceId $sceneId
    ) {
    }
}
