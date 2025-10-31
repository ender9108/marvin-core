<?php

namespace Marvin\Security\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class Lastname implements Stringable
{
    private const int MIN = 1;
    private const int MAX = 255;

    public string $value;

    public function __construct(string $lastname)
    {
        Assert::notEmpty($lastname, 'security.exceptions.SC0031.lastname_does_not_empty');
        Assert::lengthBetween($lastname, self::MIN, self::MAX, 'security.exceptions.SC0033.lastname_length_between');

        $this->value = $lastname;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
