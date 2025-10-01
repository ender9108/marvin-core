<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Story;

use Zenstruck\Foundry\Story;

class SystemStory extends Story
{
    public function build(): void
    {
        $this->buildDockers();
    }

    public function buildDockers(): void
    {

    }
}
