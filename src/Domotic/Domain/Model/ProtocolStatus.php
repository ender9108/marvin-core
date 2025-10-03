<?php

namespace Marvin\Domotic\Domain\Model;

use Marvin\Domotic\Domain\ValueObject\Identity\ProtocolStatusId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Reference;

final class ProtocolStatus
{
    public readonly ProtocolStatusId $id;

    public function __construct(
    private(set) Label $label,
    private(set) Reference $reference
) {
        $this->id = new ProtocolStatusId();
}
}
