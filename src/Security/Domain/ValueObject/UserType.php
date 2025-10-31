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
        Assert::notEmpty($type, 'security.exceptions.SC0037.user_type_empty');

        if (is_string($type)) {
            Assert::keyExists(self::TYPES, $type, 'security.exceptions.SC0038.user_type_not_exists');
            $type = self::TYPES[$type];
        } else {
            Assert::inArray($type, self::TYPES, 'security.exceptions.SC0038.user_type_not_exists');
        }

        $this->value = $type;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
