<?php

namespace Marvin\Domotic\Application\Command\Zone;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Domotic\Domain\ValueObject\Identity\ZoneId;

final readonly class DeleteZone implements SyncCommandInterface
{
    public function __construct(
        public ZoneId $id
    ) {
    }
}
