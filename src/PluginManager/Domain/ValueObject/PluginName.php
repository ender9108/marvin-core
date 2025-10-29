<?php

namespace Marvin\PluginManager\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;

final readonly class PluginName implements \Stringable
{
    public string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value, 'PM0001::::plugin_name_cannot_be_empty');
        Assert::minLength($value, 3, 'PM0002::::plugin_name_must_be_at_least_3_characters');
        Assert::maxLength($value, 100, 'PM0003::::plugin_name_must_be_at_most_100_characters');

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
