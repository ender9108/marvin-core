<?php

namespace Marvin\Domotic\Application\Command\Protocol;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Domotic\Domain\ValueObject\Identity\ProtocolStatusId;
use Marvin\Shared\Domain\ValueObject\Description;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;
use Marvin\Shared\Domain\ValueObject\Reference;

final readonly class CreateProtocol implements SyncCommandInterface
{
    public function __construct(
        public Label $label,
        public Reference $reference,
        public ProtocolStatusId $status,
        public ?Description $description = null,
        public ?Metadata $metadata = null
    ) {
    }
}
