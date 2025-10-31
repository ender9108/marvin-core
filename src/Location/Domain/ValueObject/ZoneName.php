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
        Assert::notEmpty($value, 'location.exceptions.LO0012.zone_name_cannot_be_empty');
        Assert::maxLength($value, 100, 'location.exceptions.LO0013.zone_name_cannot_exceed_100');
        Assert::minLength($value, 2, 'location.exceptions.LO0014.zone_name_cannot_be_less_than_2');

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
}
