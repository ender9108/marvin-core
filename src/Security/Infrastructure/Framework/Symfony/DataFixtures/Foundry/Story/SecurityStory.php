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

namespace Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Story;

use Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory\UserFactory;
use Zenstruck\Foundry\Story;

class SecurityStory extends Story
{
    public function build(): void
    {
        $this->createUser();
    }

    private function createUser(): void
    {
        foreach (UserFactory::getDatas() as $data) {
            UserFactory::createOne($data);
        }
    }
}
