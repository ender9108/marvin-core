<?php

namespace Marvin\PluginManager\Domain\Event;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class PluginUpdateBlocked extends AbstractDomainEvent
{
    public function __construct(
        public string $pluginId,
        public string $pluginName,
        public string $currentVersion,
        public string $blockedVersion,
        public string $reason,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'plugin_id' => $this->pluginId,
            'plugin_name' => $this->pluginName,
            'current_version' => $this->currentVersion,
            'blocked_version' => $this->blockedVersion,
            'reason' => $this->reason,
        ];
    }
}
