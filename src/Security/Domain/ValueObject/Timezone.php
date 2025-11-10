<?php

namespace Marvin\Security\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use Stringable;

final readonly class Timezone implements Stringable
{
    use ValueObjectTrait;

    public string $value;

    public function __construct(string $timezone)
    {
        Assert::notEmpty($timezone, 'security.exceptions.SC0034.timezone_does_not_empty');
        Assert::isValidTimezone($timezone, 'security.exceptions.SC0035.timezone_is_invalid');

        $this->value = $timezone;
    }

    public static function fromString(string $timezone): self
    {
        return new self($timezone);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
