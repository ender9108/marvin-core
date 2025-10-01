<?php

namespace Marvin\Shared\Domain\ValueObject;

use DateTimeInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
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

    public function equals(ExpiresAt $createdAt): bool
    {
        return $this->value === $createdAt->value;
    }

    public function __toString(): string
    {
        return $this->value->format(self::DATE_FORMAT);
    }
}
