<?php
namespace EnderLab\DddCqrsBundle\Domain;

use DateTimeImmutable;
use DateTimeInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\InvalidArgument;
use Override;
use Webmozart\Assert\Assert as WebmozartAssert;

final class Assert extends WebmozartAssert
{


    #[Override]
    protected static function reportInvalidArgument($message): void
    {
        throw new InvalidArgument($message);
    }
}
