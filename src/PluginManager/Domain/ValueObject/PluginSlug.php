<?php

namespace Marvin\PluginManager\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;

final readonly class PluginSlug
{
    public string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value, 'PM0005::::plugin_slug_cannot_be_empty');
        Assert::regex($value, '/^[a-z0-9\-]+$/', 'PM0006::::plugin_slug_must_contain_only_lowercase_letters_numbers_and_hyphens');;
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
