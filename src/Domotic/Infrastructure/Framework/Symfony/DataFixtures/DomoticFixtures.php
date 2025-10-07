<?php

namespace Marvin\Domotic\Infrastructure\Framework\Symfony\DataFixtures;

use App\Domotic\Infrastructure\DataFixtures\Foundry\Story\DomoticStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

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
