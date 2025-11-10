<?php

declare(strict_types=1);

namespace Marvin\Device\Application\Query\Device;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

/**
 * Query to get a single device by ID
 */
final readonly class GetDevice implements QueryInterface
{
    public function __construct(
        public DeviceId $deviceId
    ) {
    }
}
