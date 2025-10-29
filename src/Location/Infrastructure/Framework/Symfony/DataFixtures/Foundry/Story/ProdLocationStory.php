<?php

namespace Marvin\Location\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Story;

use Marvin\Location\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory\ZoneFactory;
use Zenstruck\Foundry\Story;

final class ProdLocationStory extends Story
{
    public function build(): void
    {
        // Pièces principales
        $salon = ZoneFactory::new()
            ->building()
            ->create()
        ;
    }
}
