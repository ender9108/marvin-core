<?php

namespace Marvin\Device\Application\Service\Acl;

use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;

final readonly class ProtocolInfo
{
    public function __construct(
        public ProtocolId $id,
        public string $label,
        public string $type,
        public bool $isEnabled,
        public ?string $status = null,
        public ?array $metadata = null
    ) {
    }
}
