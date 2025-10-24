<?php

namespace Marvin\Shared\Domain\Service;

interface SluggerInterface
{
    public function slugify(string $string): string;
}
