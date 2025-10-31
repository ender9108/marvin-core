<?php

namespace Marvin\Security\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class Timezone implements Stringable
{
    public string $value;

    public function __construct(string $timezone)
    {
        Assert::notEmpty($timezone, 'security.exceptions.SC0034.timezone_does_not_empty');
        Assert::isValidTimezone($timezone, 'security.exceptions.SC0035.timezone_is_invalid');

        $this->value = $timezone;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
