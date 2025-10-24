<?php

namespace Marvin\Security\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use Stringable;

final readonly class UserType implements ValueObjectInterface, \Stringable
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

    public function equals(UserType $type): bool
    {
        return $this->value === $type->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
