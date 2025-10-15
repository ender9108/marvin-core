<?php

namespace Marvin\System\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\MarvinManagerBundle\Reference\ManagerActionReference;
use Marvin\Shared\Domain\ValueObject\ArrayValueObjectInterface;

final readonly class ContainerAllowedActions implements ArrayValueObjectInterface
{
    public array $value;

    public function __construct(array $value = [])
    {
        Assert::notEmpty($value);
        Assert::allInArray($value, ManagerActionReference::values());

        $this->value = $value;
    }

    public function equals(ArrayValueObjectInterface $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }

    public function toArray(): array
    {
        return $this->value;
    }
}
