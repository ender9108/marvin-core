<?php

namespace Marvin\Location\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;

final readonly class ZonePath implements ValueObjectInterface
{
    public string $value;

    public array $segments;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        Assert::regex($value, '/^[a-z0-9_-]+(\/[a-z0-9_-]+)*$/i');

        $this->value = $value;
        $this->segments = explode('/', $value);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function fromSegments(array $segments): self
    {
        Assert::notEmpty($segments);
        return new self(implode('/', $segments));
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }

    public function getSegments(): array
    {
        return $this->segments;
    }

    public function getDepth(): int
    {
        return count($this->segments);
    }

    public function getLeaf(): string
    {
        return end($this->segments);
    }

    public function getRoot(): string
    {
        return $this->segments[0];
    }

    public function getParentPath(): ?self
    {
        if ($this->getDepth() === 1) {
            return null;
        }
        $parentSegments = array_slice($this->segments, 0, -1);
        return self::fromSegments($parentSegments);
    }

    public function append(string $child): self
    {
        return self::fromString($this->value . '/' . $child);
    }

    public function isChildOf(self $potentialParent): bool
    {
        return str_starts_with($this->value, $potentialParent->value . '/');
    }
}
