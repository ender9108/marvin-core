<?php

namespace Marvin\Security\Domain\ValueObject;

use DateTimeInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use Marvin\Shared\Domain\ValueObject\DatetimeValueObjectInterface;
use Stringable;

final readonly class ExpiresAt implements DatetimeValueObjectInterface, Stringable
{
    private const string DATE_FORMAT = 'Y-m-d H:i:s';

    public DateTimeInterface $value;

    public function __construct(DateTimeInterface $createdAt)
    {
        Assert::dateGreaterThanNow($createdAt);

        $this->value = $createdAt;
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value->format(self::DATE_FORMAT);
    }
}
