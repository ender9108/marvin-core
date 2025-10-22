<?php

namespace Marvin\Device\Application\Query\Scene;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

final readonly class GetScene implements QueryInterface
{
    public function __construct(
        public DeviceId $sceneId,
    ) {
    }
}
