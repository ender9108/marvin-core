<?php

namespace Marvin\Security\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class Firstname implements Stringable
{
    private const int MIN = 1;
    private const int MAX = 255;

    public string $value;

    public function __construct(string $firstname)
    {
        Assert::notEmpty($firstname, 'security.exceptions.SC0030.firstname_does_not_empty');
        Assert::lengthBetween(
            $firstname,
            self::MIN,
            self::MAX,
            'security.exceptions.SC0032.firstname_length_between'
        );

        $this->value = $firstname;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
