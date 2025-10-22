<?php

namespace Marvin\Device\Application\Query\Device;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

final readonly class GetDevice implements QueryInterface
{
    public function __construct(
        public DeviceId $deviceId
    ) {}
}
