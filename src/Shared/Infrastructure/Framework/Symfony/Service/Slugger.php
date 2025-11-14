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

namespace Marvin\Shared\Infrastructure\Framework\Symfony\Service;

use Marvin\Shared\Domain\Service\SluggerInterface;
use Symfony\Component\String\Slugger\SluggerInterface as SymfonySluggerInterface;

final readonly class Slugger implements SluggerInterface
{
    public function __construct(
        private SymfonySluggerInterface $slugger,
    ) {
    }

    public function slugify(string $string): string
    {
        return $this->slugger->slug($string);
    }
}
