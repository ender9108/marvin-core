<?php

namespace Marvin\System\Domain\Model;

use Marvin\System\Domain\ValueObject\Identity\PluginId;

final readonly class Plugin
{
    public PluginId $id;

    public function __construct(
        private(set) string $label,
        private(set) string $description,
        private(set) string $reference,
        private(set) string $version,
        private(set) PluginStatus $status
    ) {
        $this->id = new PluginId();
    }
}
