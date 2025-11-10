<?php

namespace Marvin\Security\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use Stringable;

final readonly class Lastname implements Stringable
{
    use ValueObjectTrait;

    private const int MIN = 1;
    private const int MAX = 255;

    public string $value;

    public function __construct(string $lastname)
    {
        Assert::notEmpty($lastname, 'security.exceptions.SC0031.lastname_does_not_empty');
        Assert::lengthBetween($lastname, self::MIN, self::MAX, 'security.exceptions.SC0033.lastname_length_between');

        $this->value = $lastname;
    }

    public static function fromString(string $lastname): self
    {
        return new self($lastname);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
