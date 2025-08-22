<?php

namespace App\System\Infrastructure\DataFixtures;

use App\System\Infrastructure\Foundry\Story\SystemStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SystemFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        SystemStory::load();
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
