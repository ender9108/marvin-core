<?php

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
