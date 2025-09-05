<?php
namespace EnderLab\DddCqrsBundle\Domain;

use DateTimeImmutable;
use DateTimeInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\InvalidArgument;
use Override;
use Webmozart\Assert\Assert as WebmozartAssert;

final class Assert extends WebmozartAssert
{
    public static function dateGreaterThanNow(DateTimeInterface $date): void
    {
        self::greaterThanEq($date->format('Y-m-d'), new DateTimeImmutable()->format('Y-m-d'));
    }

    #[Override]
    protected static function reportInvalidArgument($message): void
    {
        throw new InvalidArgument($message);
    }
}
