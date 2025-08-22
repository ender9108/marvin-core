<?php

namespace App\Domotic\Infrastructure\Foundry\Story;

use App\Domotic\Infrastructure\Foundry\Factory\CapabilityActionFactory;
use App\Domotic\Infrastructure\Foundry\Factory\CapabilityFactory;
use App\Domotic\Infrastructure\Foundry\Factory\ZoneFactory;
use Zenstruck\Foundry\Story;

class DomoticStory extends Story
{
    public function build(): void
    {
        $this->buildZone();
        $this->buildCapabilities();
        $this->buildCapabilityActions();
    }

    private function buildZone(): void
    {
        foreach (ZoneFactory::getDatas() as $data) {
            ZoneFactory::createOne($data);
        }
    }

    private function buildCapabilities(): void
    {
        foreach (CapabilityFactory::getDatas() as $data) {
            CapabilityFactory::createOne($data);
        }
    }

    private function buildCapabilityActions(): void
    {
        foreach (CapabilityActionFactory::getDatas() as $data) {
            CapabilityActionFactory::createOne($data);
        }
    }
}
