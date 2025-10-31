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
        Assert::notEmpty($description, 'shared.exceptions.SH0006.description_does_not_empty');
        Assert::lengthBetween(
            $description,
            self::MIN,
            self::MAX,
            'shared.exceptions.SH0007.description_length_between'
        );

        $this->value = $description;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
