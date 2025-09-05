<?php
namespace Marvin\Shared\Domain\ValueObject;

use DateTimeImmutable;
use DateTimeInterface;
use EnderLab\DddCqrsBundle\Domain\Assert;
use Stringable;

final readonly class CreatedAt implements Stringable
{
    private const string DATE_FORMAT = 'Y-m-d H:i:s';

    public DateTimeInterface $createdAt;

    public function __construct(DateTimeInterface $createdAt) {
        Assert::dateGreaterThanNow($createdAt);

        $this->createdAt = $createdAt;
    }

    public function equals(CreatedAt $createdAt): bool
    {
        return $this->createdAt === $createdAt->createdAt;
    }

    public function __toString(): string
    {
        return $this->createdAt->format(self::DATE_FORMAT);
    }
}
