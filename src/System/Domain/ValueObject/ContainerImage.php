<?php

namespace Marvin\System\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class ContainerImage implements Stringable
{
    public string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        Assert::regex($value, '/^[a-z0-9\/\-_]+:[a-z0-9\.\-_]+$/i', );

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function getName(): string
    {
        return explode(':', $this->value)[0];
    }

    public function getTag(): string
    {
        return explode(':', $this->value)[1];
    }
}
