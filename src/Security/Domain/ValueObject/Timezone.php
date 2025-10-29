<?php

namespace Marvin\Security\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class Timezone implements Stringable
{
    public string $value;

    public function __construct(string $timezone)
    {
        Assert::notEmpty($timezone);
        Assert::isValidTimezone($timezone);

        $this->value = $timezone;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
