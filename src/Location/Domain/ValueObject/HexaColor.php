<?php

namespace Marvin\Location\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class HexaColor implements Stringable
{
    public function __construct(
        public ?string $value
    ) {
        if (null !== $this->value) {
            Assert::notEmpty($value, 'location.exceptions.LO0022.hex_color_cannot_be_empty');
            ;
            Assert::regex(
                $value,
                '/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{4}|[0-9A-Fa-f]{6}|[0-9A-Fa-f]{8})$/',
                'location.exceptions.LO0023.hex_color_must_be_valid'
            );
        }
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
