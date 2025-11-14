<?php

declare(strict_types=1);

namespace EnderLab\DddCqrsBundle\Domain\ValueObject;

interface ValueObjectInterface
{
    public function equals(self|ValueObjectInterface $other): bool;
}
