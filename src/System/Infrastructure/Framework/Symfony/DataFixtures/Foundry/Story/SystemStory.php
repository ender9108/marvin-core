<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Story;

use Marvin\System\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory\PluginStatusFactory;
use Zenstruck\Foundry\Story;

class SystemStory extends Story
{
    public function build(): void
    {
        $this->buildPluginStatus();
    }

    public function buildPluginStatus(): void
    {
        foreach (PluginStatusFactory::getDatas() as $status) {
            PluginStatusFactory::createOne($status);
        }
    }
}
