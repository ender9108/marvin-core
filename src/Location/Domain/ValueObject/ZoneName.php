<?php

namespace Marvin\Location\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class ZoneName implements Stringable
{
    public string $value;

    private function __construct(
        string $value,
    ) {
        Assert::notEmpty($value, 'LO0007::::zone_cannot_be_empty');
        Assert::maxLength($value, 100, 'LO0008::::zone_name_cannot_exceed_100');
        Assert::minLength($value, 2, 'LO0009::::zone_name_cannot_be_less_than_2');

        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
