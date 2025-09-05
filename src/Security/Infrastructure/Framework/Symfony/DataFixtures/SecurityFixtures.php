<?php
namespace Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\DBAL\Logging\Middleware;
use Doctrine\Persistence\ObjectManager;
use Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Story\SecurityStory;
use Psr\Log\NullLogger;

class SecurityFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $manager
            ->getConnection()
            ->getConfiguration()
            ->setMiddlewares([new Middleware(new NullLogger())])
        ;

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
