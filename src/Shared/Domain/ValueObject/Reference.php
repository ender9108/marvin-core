<?php

namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class Reference implements Stringable
{
    private const int MIN = 3;
    private const int MAX = 64;

    public string $value;

    public function __construct(string $reference)
    {
        Assert::notEmpty($reference, 'shared.exceptions.SH0018.reference_does_not_empty');
        Assert::lengthBetween($reference, self::MIN, self::MAX, 'shared.exceptions.SH0019.reference_length_between');

        $this->value = $reference;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
