<?php

namespace Marvin\Domotic\Domain\Model;

use DateTimeImmutable;
use EnderLab\ToolsBundle\Service\ListTrait;
use Marvin\Domotic\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Domotic\Domain\ValueObject\ProtocolStatus;
use Marvin\Shared\Domain\ValueObject\CreatedAt;
use Marvin\Shared\Domain\ValueObject\Description;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;
use Marvin\Shared\Domain\ValueObject\Reference;
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

    public function update(
        ?Label $label = null,
        ?Reference $reference = null,
        ?Description $description = null,
        ?Metadata $metadata = null
    ): self {
        $this->label = $label ?? $this->label;
        $this->reference = $reference ?? $this->reference;
        $this->description = $description ?? $this->description;
        $this->metadata = $metadata ?? $this->metadata;

        return $this;
    }

    public function disable(): self
    {
        $this->status = ProtocolStatus::disabled();

        return $this;
    }

    public function enable(): self
    {
        $this->status = ProtocolStatus::enabled();

        return $this;
    }
}
