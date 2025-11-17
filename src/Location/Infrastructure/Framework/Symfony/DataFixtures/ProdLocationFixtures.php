<?php

/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

namespace Marvin\Location\Infrastructure\Framework\Symfony\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Marvin\Location\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Story\ProdLocationStory;

class ProdLocationFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        ProdLocationStory::load();
    }

    public function getOrder(): int
    {
        return 30;
    }

    public static function getGroups(): array
    {
        return ['prod'];
    }
}
