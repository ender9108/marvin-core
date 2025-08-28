<?php

namespace EnderLab\Zigbee2mqttBundle\Infrastructure\Symfony\Command;

use App\System\Infrastructure\Symfony\Command\AbstractPluginManagerCommand;

abstract class AbstractZigbeePluginManagerCommand extends AbstractPluginManagerCommand
{
    protected function getPluginRootPath(): string
    {
        return __DIR__.'/../../../../';
    }

    protected function getPluginReference(): string
    {
        return $this->parameters->get('plugin_reference');
    }

    protected function getPluginRequirements(): array
    {
        return $this->parameters->get('plugin_requirements') ?? [];
    }
}
