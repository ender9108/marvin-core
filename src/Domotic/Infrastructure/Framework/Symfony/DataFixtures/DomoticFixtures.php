<?php

namespace Marvin\Domotic\Infrastructure\Framework\Symfony\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Marvin\Domotic\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Story\DomoticStory;

class DomoticFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        DomoticStory::load();
    }

    public function getOrder(): int
    {
        return 30;
    }

    public static function getGroups(): array
    {
        return ['dev', 'test', 'prod'];
    }
}
