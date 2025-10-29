<?php

namespace Marvin\Security\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class UserStatus implements Stringable
{
    public const array STATUSES = [
        'DISABLED' => 0,
        'ENABLED' => 1,
        'LOCKED' => 2,
        'TO_DELETE' => 9,
    ];

    public int $value;

    public function __construct(string|int $status)
    {
        if (is_string($status)) {
            Assert::notEmpty($status);
            Assert::keyExists(self::STATUSES, $status);
            $status = self::STATUSES[$status];
        } else {
            Assert::inArray($status, self::STATUSES);
        }

        $this->value = $status;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function isDisabled(): bool
    {
        return $this->value === self::STATUSES['DISABLED'];
    }

    public function isEnabled(): bool
    {
        return $this->value === self::STATUSES['ENABLED'];
    }

    public function isLocked(): bool
    {
        return $this->value === self::STATUSES['LOCKED'];
    }

    public function isToDelete(): bool
    {
        return $this->value === self::STATUSES['TO_DELETE'];
    }

    public static function disabled(): self
    {
        return new self(self::STATUSES['DISABLED']);
    }

    public static function enabled(): self
    {
        return new self(self::STATUSES['ENABLED']);
    }

    public static function locked(): self
    {
        return new self(self::STATUSES['LOCKED']);
    }

    public static function toDelete(): self
    {
        return new self(self::STATUSES['TO_DELETE']);
    }

    public function __toString(): string
    {
        return (string) array_search($this->value, self::STATUSES);
    }
}
