<?php
namespace Marvin\Shared\Domain\ValueObject;

use DateTimeInterface;
use EnderLab\DddCqrsBundle\Domain\Assert;
use Stringable;

final class UpdatedAt implements Stringable
{
    private const string DATE_FORMAT = 'Y-m-d H:i:s';

    public readonly DateTimeInterface $updatedAt;

    public function __construct(DateTimeInterface $updatedAt) {
        Assert::dateGreaterThanNow($updatedAt);

        $this->updatedAt = $updatedAt;
    }

    public function equals(UpdatedAt $updatedAt): bool
    {
        return $this->updatedAt === $updatedAt->updatedAt;
    }

    public function __toString(): string
    {
        return $this->updatedAt->format(self::DATE_FORMAT);
    }
}
