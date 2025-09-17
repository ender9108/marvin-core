<?php

namespace Marvin\Shared\Domain\ValueObject;

use DateTimeInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use Stringable;

final readonly class UpdatedAt implements ValueObjectInterface, Stringable
{
    private const string DATE_FORMAT = 'Y-m-d H:i:s';

    public DateTimeInterface $value;

    public function __construct(DateTimeInterface $updatedAt)
    {
        Assert::dateGreaterThanNow($updatedAt);

        $this->value = $updatedAt;
    }

    public function equals(UpdatedAt $updatedAt): bool
    {
        return $this->value === $updatedAt->value;
    }

    public function __toString(): string
    {
        return $this->value->format(self::DATE_FORMAT);
    }
}
