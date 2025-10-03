<?php

namespace Marvin\Domotic\Domain\Model;

use DateTimeImmutable;
use Marvin\Domotic\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\CreatedAt;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Reference;
use Marvin\Shared\Domain\ValueObject\Description;
use Marvin\Shared\Domain\ValueObject\Metadata;
use Marvin\Shared\Domain\ValueObject\UpdatedAt;

final class Protocol
{
    public readonly ProtocolId $id;

    public function __construct(
        private(set) Label $label,
        private(set) Reference $reference,
        private(set) ProtocolStatus $status,
        private(set) ?Description $description = null,
        private(set) ?Metadata $metadata = null,
        private(set) ?UpdatedAt $updatedAt = null,
        public readonly CreatedAt $createdAt = new CreatedAt(new DateTimeImmutable())
    ) {
        $this->id = new ProtocolId();
    }
}
