<?php

namespace Marvin\Domotic\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use Stringable;

final readonly class Area implements ValueObjectInterface
{
    private const float MIN = 0.0;

    public float $value;

    public function __construct(float $label)
    {
        Assert::minLength($label, self::MIN);

        $this->value = $label;
    }

    public function equals(Area $label): bool
    {
        return $this->value === $label->value;
    }
}
