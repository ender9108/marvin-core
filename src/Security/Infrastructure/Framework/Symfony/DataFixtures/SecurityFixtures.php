<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Story\SecurityStory;

class SecurityFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        SecurityStory::load();
    }

    public function getOrder(): int
    {
        return 10;
    }

    public static function getGroups(): array
    {
        return ['dev', 'test'];
    }
}
