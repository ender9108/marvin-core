<?php

namespace Marvin\System\Domain\Model;

use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Reference;
use Marvin\System\Domain\ValueObject\Identity\PluginStatusId;

final readonly class PluginStatus
{
    public PluginStatusId $id;

    public function __construct(
        private(set) Label $label,
        private(set) Reference $reference
    ) {
        $this->id = new PluginStatusId();
    }
}
