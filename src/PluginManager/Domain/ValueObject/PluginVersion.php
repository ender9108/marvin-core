<?php

namespace Marvin\PluginManager\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;

final readonly class PluginVersion
{
    public string $value;

    public function __construct(string $value)
    {
        Assert::regex($value, '/^\d+\.\d+\.\d+(-[a-zA-Z0-9]+)?$/', 'PM0004::::plugin_version_must_follow_semantic_versioning');;
        $this->value = $value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function isGreaterThan(self $other): bool
    {
        return version_compare($this->value, $other->value, '>');
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
