<?php

namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class Description implements Stringable
{
    private const int MIN = 1;
    private const int MAX = 5000;

    public string $value;

    public function __construct(string $description)
    {
        Assert::notEmpty($description);
        Assert::lengthBetween($description, self::MIN, self::MAX);

        $this->value = $description;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
