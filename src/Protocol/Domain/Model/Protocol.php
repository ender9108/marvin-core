<?php

namespace Marvin\Protocol\Domain\Model;

use EnderLab\DddCqrsBundle\Domain\Model\AggregateRoot;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;

class Protocol extends AggregateRoot
{
    public readonly ProtocolId $id;

    public function __construct(
        private(set) Label $label,
        private(set) string $type,
        private(set) bool $isEnabled = false,
        private(set) ?string $status = null,
        private(set) ?Metadata $metadata = null,
    ) {
    }
}
