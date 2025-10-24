<?php

namespace Marvin\Location\Infrastructure\Framework\Symfony\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Marvin\Location\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Story\LocationStory;

class LocationFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        LocationStory::load();
    }

    public function getOrder(): int
    {
        return 30;
    }

    public static function getGroups(): array
    {
        return ['dev', 'test'];
    }
}
