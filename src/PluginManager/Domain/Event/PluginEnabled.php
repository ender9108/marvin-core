<?php

namespace Marvin\PluginManager\Domain\Event;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class PluginEnabled extends AbstractDomainEvent
{
    public function __construct(
        public string $pluginId,
        public string $pluginName,
        public string $pluginClass,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'plugin_id' => $this->pluginId,
            'plugin_name' => $this->pluginName,
            'plugin_class' => $this->pluginClass,
        ];
    }
}
