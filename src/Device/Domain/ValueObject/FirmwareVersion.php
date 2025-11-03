<?php

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use Stringable;

final readonly class FirmwareVersion implements Stringable
{
    use ValueObjectTrait;

    public string $value;

    private function __construct(
        string $value
    ) {
        Assert::notEmpty($value, 'device.exceptions.DE0035.firmware_version_empty');
        Assert::length($value, 50, 'device.exceptions.DE0036.firmware_version_length');

        $this->value = $value;
    }

    public static function fromString(?string $value): self
    {
        return new self($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

