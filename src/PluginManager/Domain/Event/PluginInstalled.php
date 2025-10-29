<?php

namespace Marvin\PluginManager\Domain\Event;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class PluginInstalled extends AbstractDomainEvent
{
    public function __construct(
        public string $pluginId,
        public string $pluginName,
        public string $pluginClass,
        public string $version,
        public array $capabilities,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'plugin_id' => $this->pluginId,
            'plugin_name' => $this->pluginName,
            'plugin_class' => $this->pluginClass,
            'version' => $this->version,
            'capabilities' => $this->capabilities,
        ];
    }
}
