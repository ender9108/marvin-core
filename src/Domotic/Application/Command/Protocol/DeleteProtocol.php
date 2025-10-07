<?php

namespace Marvin\Domotic\Application\Command\Protocol;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Domotic\Domain\ValueObject\Identity\ProtocolId;

final readonly class DeleteProtocol implements SyncCommandInterface
{
    public function __construct(
        public ProtocolId $id
    ) {
    }
}
