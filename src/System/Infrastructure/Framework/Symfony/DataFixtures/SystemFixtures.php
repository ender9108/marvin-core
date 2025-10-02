<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Story\SystemStory;

class SystemFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        SystemStory::load();
    }

    public function getOrder(): int
    {
        return 20;
    }

    public static function getGroups(): array
    {
        return ['dev', 'test'];
    }
}
