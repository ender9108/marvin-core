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

namespace Marvin\Location\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Story;

use Marvin\Location\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory\ZoneFactory;
use Zenstruck\Foundry\Story;

final class ProdLocationStory extends Story
{
    public function build(): void
    {
        // Pièces principales
        $salon = ZoneFactory::new()
            ->building()
            ->create()
        ;
    }
}
