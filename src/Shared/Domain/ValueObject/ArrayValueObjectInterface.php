<?php

namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;

interface ArrayValueObjectInterface extends ValueObjectInterface
{
    public function toArray(): array;
}
