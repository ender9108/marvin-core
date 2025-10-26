<?php

namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use Marvin\Shared\Domain\Application;
use Stringable;

final readonly class ProtocolType implements ValueObjectInterface, Stringable
{
    public readonly string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        Assert::inArray($value, Application::APP_PROTOCOL_TYPES_AVAILABLES);

        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
