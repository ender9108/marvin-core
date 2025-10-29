<?php

namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Marvin\Shared\Domain\Application;
use Stringable;

final readonly class ProtocolType implements Stringable
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

    public function __toString(): string
    {
        return $this->value;
    }
}
