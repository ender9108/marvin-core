<?php

namespace Marvin\Security\Domain\ValueObject;

use DateTimeInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class ExpiresAt implements Stringable
{
    private const string DATE_FORMAT = 'Y-m-d H:i:s';

    public DateTimeInterface $value;

    public function __construct(DateTimeInterface $expiresAt)
    {
        Assert::dateGreaterThanNow($expiresAt, 'security.exception.SC0036.expires_at_must_be_gt_now');

        $this->value = $expiresAt;
    }

    public function __toString(): string
    {
        return $this->value->format(self::DATE_FORMAT);
    }
}
