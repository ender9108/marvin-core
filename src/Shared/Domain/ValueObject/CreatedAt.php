<?php

namespace Marvin\Shared\Domain\ValueObject;

use DateTimeImmutable;
use DateTimeInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use Stringable;

final readonly class CreatedAt implements DatetimeValueObjectInterface, Stringable
{
    private const string DATE_FORMAT = 'Y-m-d H:i:s';

    public DateTimeInterface $value;

    public function __construct(DateTimeInterface $createdAt)
    {
        Assert::dateGreaterThanNow($createdAt);

        $this->value = $createdAt;
    }

    public function equals(CreatedAt $createdAt): bool
    {
        return $this->value === $createdAt->value;
    }

    public function __toString(): string
    {
        return $this->value->format(self::DATE_FORMAT);
    }
}
