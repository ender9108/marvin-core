<?php

namespace Marvin\Secret\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use Stringable;

final readonly class SecretKey implements ValueObjectInterface, Stringable
{
    public string $value;

    public function __construct(string $value) {
        Assert::regex($value, '/^[a-z0-9_]{3,100}$/');

        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function equals(SecretKey $secretKey): bool
    {
        return $this->value === $secretKey->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
