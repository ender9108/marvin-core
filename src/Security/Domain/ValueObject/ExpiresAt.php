<?php

namespace Marvin\Security\Domain\ValueObject;

use DateTimeInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class ExpiresAt implements Stringable
{
    private const string DATE_FORMAT = 'Y-m-d H:i:s';

    public DateTimeInterface $value;

    public function __construct(DateTimeInterface $createdAt)
    {
        Assert::dateGreaterThanNow($createdAt);

        $this->value = $createdAt;
    }

    public function __toString(): string
    {
        return $this->value->format(self::DATE_FORMAT);
    }
}
