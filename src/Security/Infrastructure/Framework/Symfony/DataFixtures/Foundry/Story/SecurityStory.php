<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Story;

use Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory\UserFactory;
use Zenstruck\Foundry\Story;

class SecurityStory extends Story
{
    public function build(): void
    {
        $this->createUser();
    }

    private function createUser(): void
    {
        foreach (UserFactory::getDatas() as $data) {
            UserFactory::createOne($data);
        }
    }
}
