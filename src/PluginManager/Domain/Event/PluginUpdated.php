<?php

namespace Marvin\PluginManager\Domain\Event;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class PluginUpdated extends AbstractDomainEvent
{
    public function __construct(
        public string $pluginId,
        public string $pluginName,
        public string $oldVersion,
        public string $newVersion,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'plugin_id' => $this->pluginId,
            'plugin_name' => $this->pluginName,
            'old_version' => $this->oldVersion,
            'new_version' => $this->newVersion,
        ];
    }
}
