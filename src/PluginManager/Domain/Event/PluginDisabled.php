<?php

namespace Marvin\PluginManager\Domain\Event;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class PluginDisabled extends AbstractDomainEvent
{
    public function __construct(
        public readonly string $pluginId,
        public readonly string $pluginName,
        public readonly ?string $reason,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'plugin_id' => $this->pluginId,
            'plugin_name' => $this->pluginName,
            'reason' => $this->reason,
        ];
    }
}
