<?php

namespace App\Shared\Infrastructure\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\DBAL\Logging\Middleware;
use Doctrine\Persistence\ObjectManager;
use Psr\Log\NullLogger;

class SharedFixtures extends Fixture implements OrderedFixtureInterface, FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $manager
            ->getConnection()
            ->getConfiguration()
            ->setMiddlewares([new Middleware(new NullLogger())])
        ;
    }

    public function getOrder(): int
    {
        return 1;
    }

    public static function getGroups(): array
    {
        return ['dev', 'test', 'prod'];
    }
}
