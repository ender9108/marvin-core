<?php

namespace Marvin\Security\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class UserType implements Stringable
{
    public const array TYPES = [
        'APP' => 1,
        'CLI' => 2
    ];

    public int $value;

    public function __construct(string|int $type)
    {
        Assert::notEmpty($type);

        if (is_string($type)) {
            Assert::keyExists(self::TYPES, $type);
            $type = self::TYPES[$type];
        } else {
            Assert::inArray($type, self::TYPES);
        }

        $this->value = $type;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
